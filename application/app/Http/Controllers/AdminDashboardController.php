<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgencyUpdateRequest;
use App\Models\Agency;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Vehicle;
use App\Notifications\AppNotification;
use App\Services\ReservationTransitionService;
use App\Support\SafeNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    protected function ensureSuperAdmin(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->isSuperAdmin()) {
            abort(403, 'Vous n\'êtes pas autorisé à accéder à ce tableau de bord.');
        }
    }

    public function index(): View
    {
        $this->ensureSuperAdmin();

        $startDate = now()->startOfMonth();
        $endDate = now()->endOfMonth();
        $previousStartDate = now()->copy()->subMonthNoOverflow()->startOfMonth();
        $previousEndDate = now()->copy()->subMonthNoOverflow()->endOfMonth();

        $totalRevenue = (float) Reservation::query()
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');

        $currentMonthRevenue = (float) Reservation::query()
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->sum('total_price');

        $previousMonthRevenue = (float) Reservation::query()
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('created_at', [$previousStartDate, $previousEndDate])
            ->sum('total_price');

        $revenueGrowth = $previousMonthRevenue > 0
            ? round((($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100)
            : ($currentMonthRevenue > 0 ? 100 : 0);

        $activeAgencies = Agency::query()->active()->count();
        $pendingAgenciesCount = Agency::query()->pending()->count();
        $totalUsers = User::query()->count();
        $totalVehicles = Vehicle::query()->count();
        $totalReservations = Reservation::query()->count();

        $pendingAgencies = Agency::query()
            ->withCount('vehicles')
            ->with('founder')
            ->pending()
            ->latest()
            ->take(5)
            ->get();

        $recentUsers = User::query()
            ->latest()
            ->take(4)
            ->get()
            ->map(function (User $recentUser) {
                return [
                    'id' => $recentUser->id,
                    'name' => $recentUser->full_name,
                    'initials' => strtoupper(
                        substr($recentUser->first_name, 0, 1).substr($recentUser->last_name, 0, 1)
                    ),
                    'joined_at' => $recentUser->created_at,
                    'joined_since' => $recentUser->created_at?->diffForHumans(),
                    'is_verified' => $recentUser->hasVerifiedEmail(),
                    'status_label' => $recentUser->hasVerifiedEmail() ? 'Verified' : 'Pending',
                    'profile_photo_url' => $recentUser->profile_photo_url,
                ];
            });

        $vehicleStatusCounts = Vehicle::query()
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $availableVehicles = (int) ($vehicleStatusCounts['available'] ?? 0);
        $unavailableVehicles = (int) ($vehicleStatusCounts['unavailable'] ?? 0);

        $vehicleSegmentsRaw = [
            'available' => $availableVehicles,
            'unavailable' => $unavailableVehicles,
        ];

        $vehicleDistribution = collect($vehicleSegmentsRaw)->map(
            fn (int $count) => $totalVehicles > 0 ? round(($count / $totalVehicles) * 100) : 0
        );

        $monthlyRevenueOverview = Reservation::query()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month_key')
            ->selectRaw('SUM(total_price) as gross_revenue')
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereBetween('created_at', [now()->copy()->subMonths(6)->startOfMonth(), $endDate])
            ->groupBy('month_key')
            ->orderBy('month_key')
            ->get()
            ->keyBy('month_key');

        $platformFeeRate = 0.10;
        $revenueOverview = collect(range(6, 0))->map(function (int $monthOffset) use ($monthlyRevenueOverview, $platformFeeRate) {
            $month = now()->copy()->subMonths($monthOffset);
            $monthKey = $month->format('Y-m');
            $grossRevenue = (float) optional($monthlyRevenueOverview->get($monthKey))->gross_revenue;

            return [
                'label' => $month->format('M'),
                'commissions' => round($grossRevenue, 2),
                'extra_fees' => round($grossRevenue * $platformFeeRate, 2),
            ];
        })->values();

        $recentReservations = Reservation::query()
            ->with(['agency', 'vehicle', 'client'])
            ->latest()
            ->take(3)
            ->get();

        $newAgencies = Agency::query()
            ->latest()
            ->take(2)
            ->get();

        $activityLog = collect()
            ->merge($recentReservations->map(function (Reservation $reservation) {
                return [
                    'type' => 'reservation',
                    'icon' => 'calendar_month',
                    'icon_background' => 'bg-primary-container/10',
                    'icon_color' => 'text-primary-container',
                    'message' => sprintf(
                        '%s booked %s at %s.',
                        $reservation->client?->full_name ?? 'A client',
                        trim(($reservation->vehicle?->brand ?? '').' '.($reservation->vehicle?->model ?? 'vehicle')),
                        $reservation->agency?->name ?? 'an agency'
                    ),
                    'occurred_at' => $reservation->created_at,
                ];
            }))
            ->merge($newAgencies->map(function (Agency $agency) {
                return [
                    'type' => 'agency',
                    'icon' => 'storefront',
                    'icon_background' => 'bg-secondary-container/20',
                    'icon_color' => 'text-secondary',
                    'message' => sprintf('%s registered in %s.', $agency->name, $agency->city),
                    'occurred_at' => $agency->created_at,
                ];
            }))
            ->merge($recentUsers->take(2)->map(function (array $recentUser) {
                return [
                    'type' => 'user',
                    'icon' => 'person_add',
                    'icon_background' => 'bg-tertiary-fixed/20',
                    'icon_color' => 'text-on-tertiary-container',
                    'message' => sprintf('%s created a new account.', $recentUser['name']),
                    'occurred_at' => $recentUser['joined_at'],
                ];
            }))
            ->sortByDesc('occurred_at')
            ->take(6)
            ->values()
            ->map(function (array $activity) {
                $activity['time_label'] = $activity['occurred_at']?->diffForHumans();

                return $activity;
            });

        return view('admin.dashboard', [
            'dateRange' => [
                'start' => $startDate,
                'end' => $endDate,
                'label' => $startDate->format('M j, Y').' - '.$endDate->format('M j, Y'),
            ],
            'kpis' => [
                'totalRevenue' => $totalRevenue,
                'revenueGrowth' => $revenueGrowth,
                'activeAgencies' => $activeAgencies,
                'pendingAgencies' => $pendingAgenciesCount,
                'totalUsers' => $totalUsers,
                'totalVehicles' => $totalVehicles,
                'totalReservations' => $totalReservations,
                'availableVehicles' => $availableVehicles,
                'unavailableVehicles' => $unavailableVehicles,
            ],
            'pendingAgenciesCount' => $pendingAgenciesCount,
            'pendingAgencies' => $pendingAgencies,
            'recentUsers' => $recentUsers,
            'activityLog' => $activityLog,
            'revenueOverview' => $revenueOverview,
            'vehicleDistribution' => [
                'total' => $totalVehicles,
                'counts' => $vehicleSegmentsRaw,
                'percentages' => $vehicleDistribution,
            ],
        ]);
    }

    public function agencies(Request $request): View
    {
        $this->ensureSuperAdmin();

        $adminUser = auth()->user();
        $statusFilter = $request->string('status')->toString();
        $allowedStatuses = ['active', 'pending', 'rejected', 'suspended'];

        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'all';
        }

        $agencies = Agency::query()
            ->with(['founder'])
            ->withCount(['vehicles', 'reservations'])
            ->withSum([
                'reservations as confirmed_revenue' => function ($query) {
                    $query->whereIn('status', ['confirmed', 'completed']);
                },
            ], 'total_price')
            ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->latest()
            ->paginate(12)->withQueryString();

        $agencyCounts = Agency::query()->select('status')->selectRaw('COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.agencies', [
            'adminUser' => $adminUser,
            'agencies' => $agencies,
            'currentStatusFilter' => $statusFilter,
            'totalAgencies' => (int) $agencyCounts->sum(),
            'activeAgenciesCount' => (int) ($agencyCounts['active'] ?? 0),
            'pendingAgenciesCount' => (int) ($agencyCounts['pending'] ?? 0),
            'rejectedAgenciesCount' => (int) ($agencyCounts['rejected'] ?? 0),
            'suspendedAgenciesCount' => (int) ($agencyCounts['suspended'] ?? 0),
        ]);
    }

    public function users(Request $request): View
    {
        $this->ensureSuperAdmin();

        $adminUser = auth()->user();
        $roleFilter = $request->string('role')->toString();
        $allowedRoles = ['super_admin', 'agency_owner', 'client', 'visitor'];

        if (! in_array($roleFilter, $allowedRoles, true)) {
            $roleFilter = 'all';
        }

        $users = User::query()
            ->with(['agency'])
            ->withCount('reservations')
            ->when($roleFilter !== 'all', function ($query) use ($roleFilter) {
                $query->where('role', $roleFilter);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $userStats = User::query()->selectRaw("COUNT(*) as total, SUM(is_active = 1) as active, SUM(email_verified_at IS NOT NULL) as verified, SUM(role = 'agency_owner') as owners, SUM(role = 'client') as clients")->first();

        return view('admin.users', [
            'adminUser' => $adminUser,
            'users' => $users,
            'currentRoleFilter' => $roleFilter,
            'pendingAgenciesCount' => Agency::query()->pending()->count(),
            'totalUsers' => (int) $userStats->total,
            'activeUsersCount' => (int) $userStats->active,
            'verifiedUsersCount' => (int) $userStats->verified,
            'agencyOwnersCount' => (int) $userStats->owners,
            'clientsCount' => (int) $userStats->clients,
        ]);
    }

    public function vehicles(Request $request): View
    {
        $this->ensureSuperAdmin();

        $adminUser = auth()->user();
        $statusFilter = $request->string('status')->toString();
        $allowedStatuses = ['available', 'unavailable'];

        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'all';
        }

        $vehiclesQuery = Vehicle::query()
            ->with(['agency', 'primaryPhoto'])
            ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($vehicleQuery) use ($search) {
                    $vehicleQuery->where('brand', 'like', '%'.$search.'%')
                        ->orWhere('model', 'like', '%'.$search.'%')
                        ->orWhere('registration_number', 'like', '%'.$search.'%')
                        ->orWhereHas('agency', function ($agencyQuery) use ($search) {
                            $agencyQuery->where('name', 'like', '%'.$search.'%')
                                ->orWhere('city', 'like', '%'.$search.'%');
                        });
                });
            })
            ->latest();

        $vehicles = $vehiclesQuery
            ->paginate(12)
            ->withQueryString();

        $vehicleStats = Vehicle::query()->selectRaw("COUNT(*) as total, SUM(status = 'available') as available, SUM(status = 'unavailable') as unavailable, SUM(is_featured = 1) as featured, SUM(has_gps = 1) as gps")->first();

        return view('admin.vehicles', [
            'adminUser' => $adminUser,
            'vehicles' => $vehicles,
            'currentStatusFilter' => $statusFilter,
            'pendingAgenciesCount' => Agency::query()->pending()->count(),
            'totalVehicles' => (int) $vehicleStats->total,
            'availableVehiclesCount' => (int) $vehicleStats->available,
            'unavailableVehiclesCount' => (int) $vehicleStats->unavailable,
            'featuredVehiclesCount' => (int) $vehicleStats->featured,
            'gpsVehiclesCount' => (int) $vehicleStats->gps,
        ]);
    }

    public function reservations(Request $request): View
    {
        $this->ensureSuperAdmin();

        $adminUser = auth()->user();
        $statusFilter = $request->string('status')->toString();
        $allowedStatuses = ['pending', 'confirmed', 'completed', 'cancelled', 'rejected'];

        if (! in_array($statusFilter, $allowedStatuses, true)) {
            $statusFilter = 'all';
        }

        $reservationsQuery = Reservation::query()
            ->with(['client', 'agency', 'vehicle'])
            ->when($statusFilter !== 'all', function ($query) use ($statusFilter) {
                $query->where('status', $statusFilter);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim((string) $request->search);

                $query->where(function ($reservationQuery) use ($search) {
                    $reservationQuery->where('reference', 'like', '%'.$search.'%')
                        ->orWhereHas('client', function ($clientQuery) use ($search) {
                            $clientQuery->where('first_name', 'like', '%'.$search.'%')
                                ->orWhere('last_name', 'like', '%'.$search.'%')
                                ->orWhere('email', 'like', '%'.$search.'%')
                                ->orWhere('cin_number', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('agency', function ($agencyQuery) use ($search) {
                            $agencyQuery->where('name', 'like', '%'.$search.'%');
                        })
                        ->orWhereHas('vehicle', function ($vehicleQuery) use ($search) {
                            $vehicleQuery->where('brand', 'like', '%'.$search.'%')
                                ->orWhere('model', 'like', '%'.$search.'%')
                                ->orWhere('registration_number', 'like', '%'.$search.'%');
                        });
                });
            })
            ->when($request->filled('date_from'), function ($query) use ($request) {
                $query->whereDate('start_date', '>=', $request->date_from);
            })
            ->when($request->filled('date_to'), function ($query) use ($request) {
                $query->whereDate('end_date', '<=', $request->date_to);
            })
            ->latest();

        $reservations = $reservationsQuery
            ->paginate(12)
            ->withQueryString();

        $reservationCounts = Reservation::query()->select('status')->selectRaw('COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.reservations', [
            'adminUser' => $adminUser,
            'reservations' => $reservations,
            'currentStatusFilter' => $statusFilter,
            'pendingAgenciesCount' => Agency::query()->pending()->count(),
            'totalReservations' => (int) $reservationCounts->sum(),
            'pendingReservationsCount' => (int) ($reservationCounts['pending'] ?? 0),
            'confirmedReservationsCount' => (int) ($reservationCounts['confirmed'] ?? 0),
            'completedReservationsCount' => (int) ($reservationCounts['completed'] ?? 0),
            'cancelledReservationsCount' => (int) ($reservationCounts['cancelled'] ?? 0),
            'totalReservationsRevenue' => (float) Reservation::query()
                ->whereIn('status', ['confirmed', 'completed'])
                ->sum('total_price'),
        ]);
    }

    public function editAgency(string $slug): View
    {
        $this->ensureSuperAdmin();

        $agency = Agency::query()
            ->with('founder')
            ->where('slug', $slug)
            ->firstOrFail();

        $cities = json_decode(file_get_contents(app_path('ma.json')), true);

        return view('admin.edit-agency', [
            'agency' => $agency,
            'cities' => $cities,
            'adminUser' => auth()->user(),
            'pendingAgenciesCount' => Agency::query()->pending()->count(),
        ]);
    }

    public function updateAgency(AgencyUpdateRequest $request, string $slug): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $agency = Agency::query()->where('slug', $slug)->firstOrFail();
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $agency->name = $data['name'];
        $agency->city = $data['city'];
        $agency->address = $data['address'];
        $agency->phone = $data['phone'];
        $agency->email = $data['email'];
        $agency->logo = $data['logo'] ?? $agency->logo;
        $agency->description = $data['description'] ?? null;
        $agency->save();

        return redirect()
            ->route('admin.agencies')
            ->with('success', "Les informations de l'agence {$agency->name} ont été mises a jour.");
    }

    public function destroyAgency(string $slug): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $agency = Agency::query()->where('slug', $slug)->firstOrFail();
        $agencyName = $agency->name;

        if ($agency->logo) {
            Storage::disk('public')->delete($agency->logo);
        }

        $agency->delete();

        return redirect()
            ->route('admin.agencies')
            ->with('success', "L'agence {$agencyName} a été supprimée.");
    }

    public function approveAgency(Agency $agency): RedirectResponse
    {
        $this->ensureSuperAdmin();

        if (! $agency->isPending()) {
            return redirect()
                ->back()
                ->with('error', 'Cette agence ne peut plus être approuvée.');
        }

        $agency->update([
            'status' => 'active',
            'approved_at' => now(),
            'rejected_at' => null,
            'rejection_reason' => null,
        ]);

        // --- NOTIFICATION AU PROPRIÉTAIRE ---
        SafeNotifier::send($agency->founder, new AppNotification([
            'subject' => 'Félicitations ! Votre agence est approuvée',
            'body' => "Votre agence {$agency->name} a été validée. Nous sommes ravis de vous compter parmi nos partenaires.commencez à répertorier vos véhicules pour rendre votre catalogue visible auprès de nos clients.",
            'url' => '/dashboard',
        ]), ['event' => 'agency_approved']);

        return redirect()
            ->back()
            ->with('success', "L'agence {$agency->name} a été approuvée avec succès.");
    }

    public function rejectAgency(Request $request, Agency $agency): RedirectResponse
    {
        $this->ensureSuperAdmin();

        if (! $agency->isPending()) {
            return redirect()
                ->back()
                ->with('error', 'Cette agence ne peut plus être refusée.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        $agency->update([
            'status' => 'rejected',
            'approved_at' => null,
            'rejected_at' => now(),
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // --- NOTIFICATION AU PROPRIÉTAIRE ---
        SafeNotifier::send($agency->founder, new AppNotification([
            'subject' => 'Mise à jour : Votre demande d\'agence',
            'body' => "Malheureusement, votre demande pour l'agence {$agency->name} a été refusée. Motif : ".$validated['rejection_reason'],
            'url' => '/contact', // Ou vers la page d'édition de l'agence
        ]), ['event' => 'agency_rejected']);

        return redirect()
            ->back()
            ->with('success', "L'agence {$agency->name} a été refusée.");
    }

    public function suspendAgency(Agency $agency): RedirectResponse
    {
        $this->ensureSuperAdmin();

        if ($agency->status === 'suspended') {
            return redirect()
                ->back()
                ->with('error', 'Cette agence est deja suspendue.');
        }

        if ($agency->status === 'pending') {
            return redirect()
                ->back()
                ->with('error', 'Une agence en attente doit etre approuvee ou refusee avant suspension.');
        }

        $agency->update([
            'status' => 'suspended',
        ]);

        // --- NOTIFICATION AU PROPRIÉTAIRE ---
        SafeNotifier::send($agency->founder, new AppNotification([
            'subject' => 'Alerte : Suspension de votre agence',
            'body' => "Votre agence {$agency->name} a été suspendue temporairement par l'administrateur. Vos véhicules ne sont plus visibles pour le moment.",
            'url' => '/contact', // Lien vers le support ou les CGU
        ]), ['event' => 'agency_suspended']);

        return redirect()
            ->back()
            ->with('success', "L'agence {$agency->name} a ete suspendue.");
    }

    public function confirmReservation(Reservation $reservation): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $updated = app(ReservationTransitionService::class)->confirm($reservation);
        if (! $updated) {
            return redirect()
                ->back()
                ->with('error', 'Cette reservation ne peut pas etre confirmee.');
        }

        $reservation = $updated;

        return redirect()
            ->back()
            ->with('success', "La reservation {$reservation->reference} a ete confirmee.");
    }

    public function cancelReservation(Reservation $reservation): RedirectResponse
    {
        $this->ensureSuperAdmin();

        $updated = app(ReservationTransitionService::class)->cancel($reservation, 'super_admin');
        if (! $updated) {
            return redirect()
                ->back()
                ->with('error', 'Cette reservation ne peut pas etre annulee.');
        }

        $reservation = $updated;

        return redirect()
            ->back()
            ->with('success', "La reservation {$reservation->reference} a ete annulee.");
    }
}
