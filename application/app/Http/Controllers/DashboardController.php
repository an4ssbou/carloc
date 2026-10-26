<?php

namespace App\Http\Controllers;

use App\Models\Agency;
use App\Models\Reservation;
use App\Models\Vehicle;
use App\Notifications\AppNotification;
use App\Services\ReservationTransitionService;
use App\Support\SafeNotifier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(string $slug): View
    {
        $agency = $this->resolveOwnedAgency($slug);
        $weekSql=DB::connection()->getDriverName()==='sqlite' ? "CAST((CAST(strftime('%d', start_date) AS INTEGER) - 1) / 7 AS INTEGER) + 1" : 'FLOOR((DAY(start_date) - 1) / 7) + 1';
        $daySql=DB::connection()->getDriverName()==='sqlite' ? "CASE strftime('%w',start_date) WHEN '0' THEN 'Sunday' WHEN '1' THEN 'Monday' WHEN '2' THEN 'Tuesday' WHEN '3' THEN 'Wednesday' WHEN '4' THEN 'Thursday' WHEN '5' THEN 'Friday' ELSE 'Saturday' END" : 'DAYNAME(start_date)';

        $thisMonthData = Reservation::where('agency_id', $agency->id)
            ->whereBetween('start_date', [
                now()->copy()->startOfMonth(),
                now()->copy()->endOfMonth(),
            ])
            ->whereIn('status', ['confirmed', 'completed'])
            ->selectRaw($weekSql.' as week_num, SUM(total_price) as total')
            ->groupBy('week_num')
            ->pluck('total', 'week_num');

        // Même chose pour le mois dernier
        $lastMonthData = Reservation::where('agency_id', $agency->id)
            ->whereBetween('start_date', [
                now()->copy()->subMonthNoOverflow()->startOfMonth(),
                now()->copy()->subMonthNoOverflow()->endOfMonth(),
            ])
            ->whereIn('status', ['confirmed', 'completed'])
            ->selectRaw($weekSql.' as week_num, SUM(total_price) as total')
            ->groupBy('week_num')
            ->pluck('total', 'week_num');

        $revenueThisMonth = [];
        $revenueLastMonth = [];

        for ($week = 1; $week <= 5; $week++) {
            $revenueThisMonth[] = (float) $thisMonthData->get($week, 0);
            $revenueLastMonth[] = (float) $lastMonthData->get($week, 0);
        }

        $fleetStatus = Vehicle::where('agency_id', $agency->id)
            ->select('status', DB::raw('COUNT(id) as total'))
            ->groupBy('status')
            ->pluck('total', 'status');

        $fleetOnRental = $fleetStatus['unavailable'] ?? 0;
        $fleetAvailable = $fleetStatus['available'] ?? 0;

        $fleetTotal = $fleetOnRental + $fleetAvailable;

        // ✅ 3. Reservations by day — 7 derniers jours
        $reservationsParJour = Reservation::where('agency_id', $agency->id)
            ->select(
                DB::raw($daySql.' as jour'),
                DB::raw('COUNT(id) as total')
            )
            ->whereBetween('start_date', [now()->subDays(6)->startOfDay(), now()->endOfDay()])
            ->groupBy('jour')
            ->pluck('total', 'jour');

        $jours = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        $joursLabels = ['MON', 'TUE', 'WED', 'THU', 'FRI', 'SAT', 'SUN'];
        $dataReservationsJour = array_map(
            fn ($j) => $reservationsParJour[$j] ?? 0,
            $jours
        );

        // ✅ 4. KPIs
        $totalRevenue = Reservation::where('agency_id', $agency->id)
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');

        $activeReservations = Reservation::where('agency_id', $agency->id)
            ->where('status', 'confirmed')
            ->count();

        $pendingReservations = Reservation::where('agency_id', $agency->id)
            ->where('status', 'pending')
            ->count();

        $completedReservations = Reservation::where('agency_id', $agency->id)
            ->where('status', 'completed')
            ->count();

        $topVehicles = Vehicle::where('agency_id', $agency->id)
            ->withSum(['reservations' => function ($query) {
                $query->whereIn('status', ['confirmed', 'completed']); // 👈
            }], 'total_price')
            ->orderBy('reservations_sum_total_price', 'desc')
            ->limit(3)
            ->get();

        // Réservations récentes
        $recentReservations = Reservation::where('agency_id', $agency->id)
            ->with(['client', 'vehicle'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 WHEN status = 'confirmed' THEN 1 ELSE 2 END")
            ->latest()
            ->limit(10)
            ->get();

        $latestReservationRequests = Reservation::where('agency_id', $agency->id)
            ->with(['client', 'vehicle'])
            ->latest()
            ->limit(4)
            ->get()
            ->map(function (Reservation $reservation) {
                $vehicleName = trim(($reservation->vehicle?->brand ?? '').' '.($reservation->vehicle?->model ?? ''));

                return [
                    'type' => 'reservation_created',
                    'icon' => 'calendar_month',
                    'dot_color' => 'bg-secondary',
                    'message' => sprintf(
                        'Nouvelle reservation %s creee par %s pour %s.',
                        $reservation->reference ?? '#-',
                        $reservation->client?->full_name ?? 'un client',
                        $vehicleName !== '' ? $vehicleName : 'un vehicule'
                    ),
                    'occurred_at' => $reservation->created_at,
                ];
            });

        $latestReservationUpdates = Reservation::where('agency_id', $agency->id)
            ->with(['client', 'vehicle'])
            ->where(function ($query) {
                $query->whereNotNull('confirmed_at')
                    ->orWhereNotNull('cancelled_at')
                    ->orWhereNotNull('completed_at');
            })
            ->latest('updated_at')
            ->limit(6)
            ->get()
            ->map(function (Reservation $reservation) {
                $vehicleName = trim(($reservation->vehicle?->brand ?? '').' '.($reservation->vehicle?->model ?? ''));

                return match ($reservation->status) {
                    'confirmed' => [
                        'type' => 'reservation_confirmed',
                        'icon' => 'check_circle',
                        'dot_color' => 'bg-emerald-500',
                        'message' => sprintf(
                            'Reservation %s confirmee pour %s.',
                            $reservation->reference ?? '#-',
                            $vehicleName !== '' ? $vehicleName : 'un vehicule'
                        ),
                        'occurred_at' => $reservation->confirmed_at,
                    ],
                    'cancelled' => [
                        'type' => 'reservation_cancelled',
                        'icon' => 'cancel',
                        'dot_color' => 'bg-error',
                        'message' => sprintf(
                            'Reservation %s annulee%s.',
                            $reservation->reference ?? '#-',
                            $reservation->cancelled_by ? ' par '.$reservation->cancelled_by : ''
                        ),
                        'occurred_at' => $reservation->cancelled_at,
                    ],
                    'completed' => [
                        'type' => 'reservation_completed',
                        'icon' => 'task_alt',
                        'dot_color' => 'bg-tertiary-fixed-dim',
                        'message' => sprintf(
                            'Reservation %s terminee pour %s.',
                            $reservation->reference ?? '#-',
                            $vehicleName !== '' ? $vehicleName : 'un vehicule'
                        ),
                        'occurred_at' => $reservation->completed_at,
                    ],
                    default => null,
                };
            })
            ->filter();

        $recentVehicles = Vehicle::where('agency_id', $agency->id)
            ->latest()
            ->limit(3)
            ->get()
            ->map(function (Vehicle $vehicle) {
                return [
                    'type' => 'vehicle_created',
                    'icon' => 'directions_car',
                    'dot_color' => 'bg-primary',
                    'message' => sprintf(
                        'Vehicule ajoute au parc: %s %s.',
                        $vehicle->brand,
                        $vehicle->model
                    ),
                    'occurred_at' => $vehicle->created_at,
                ];
            });

        $activityLog = collect()
            ->merge($latestReservationRequests)
            ->merge($latestReservationUpdates)
            ->merge($recentVehicles)
            ->filter(fn (array $activity) => ! empty($activity['occurred_at']))
            ->sortByDesc('occurred_at')
            ->take(6)
            ->values()
            ->map(function (array $activity) {
                $activity['time_label'] = $activity['occurred_at']?->diffForHumans();

                return $activity;
            });

        return view('agency.dashboard', [
            'agency' => $agency,
            // KPIs
            'totalRevenue' => $totalRevenue,
            'activeReservations' => $activeReservations,
            'pendingReservations' => $pendingReservations,
            'completedReservations' => $completedReservations,
            'fleetTotal' => $fleetTotal,
            'fleetAvailable' => $fleetAvailable,
            'topVehicles' => $topVehicles,
            'recentReservations' => $recentReservations,

            // Charts
            'revenueThisMonth' => $revenueThisMonth,
            'revenueLastMonth' => $revenueLastMonth,
            'fleetOnRental' => $fleetOnRental,
            'joursLabels' => $joursLabels,
            'dataReservationsJour' => $dataReservationsJour,
            'activityLog' => $activityLog,
        ]);
    }

    public function reservations(Request $request, string $slug): View
    {
        $agency = $this->resolveOwnedAgency($slug);

        $baseReservationsQuery = Reservation::where('agency_id', $agency->id);

        $pendingReservations = (clone $baseReservationsQuery)
            ->where('status', 'pending')
            ->count();

        $confirmedReservations = (clone $baseReservationsQuery)
            ->where('status', 'confirmed')
            ->count();

        $cancelledReservations = (clone $baseReservationsQuery)
            ->where('status', 'cancelled')
            ->count();

        $completedReservations = (clone $baseReservationsQuery)
            ->where('status', 'completed')
            ->count();

        $reservationsQuery = Reservation::where('agency_id', $agency->id)
            ->with(['client', 'vehicle'])
            ->latest();

        if ($request->filled('status')) {
            $reservationsQuery->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);

            $reservationsQuery->where(function ($query) use ($search) {
                $query->where('reference', 'like', '%'.$search.'%')
                    ->orWhereHas('client', function ($clientQuery) use ($search) {
                        $clientQuery->where('first_name', 'like', '%'.$search.'%')
                            ->orWhere('last_name', 'like', '%'.$search.'%')
                            ->orWhere('email', 'like', '%'.$search.'%')
                            ->orWhere('cin_number', 'like', '%'.$search.'%');
                    })
                    ->orWhereHas('vehicle', function ($vehicleQuery) use ($search) {
                        $vehicleQuery->where('brand', 'like', '%'.$search.'%')
                            ->orWhere('model', 'like', '%'.$search.'%')
                            ->orWhere('registration_number', 'like', '%'.$search.'%');
                    });
            });
        }

        if ($request->filled('date_from')) {
            $reservationsQuery->whereDate('start_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $reservationsQuery->whereDate('end_date', '<=', $request->date_to);
        }

        $reservations = $reservationsQuery->paginate(12)->withQueryString();
        $fleetTotal = Vehicle::where('agency_id', $agency->id)->count();

        return view('agency.reservations', compact(
            'agency',
            'reservations',
            'pendingReservations',
            'confirmedReservations',
            'cancelledReservations',
            'completedReservations',
            'fleetTotal',
        ));
    }

    public function confirmReservation(string $slug, Reservation $reservation): RedirectResponse
    {
        $agency = $this->resolveOwnedAgency($slug);
        $this->ensureAgencyReservation($agency, $reservation);

        $updated = app(ReservationTransitionService::class)->confirm($reservation);
        if (! $updated) {
            return redirect()
                ->route('agency.dashboard', $agency->slug)
                ->with('error', 'Cette reservation ne peut pas etre confirmee.');
        }

        $reservation = $updated;

        // Notification au client (Utilise ta relation client())
        SafeNotifier::send($reservation->client, new AppNotification([
            'subject' => 'Réservation confirmée ! ✅',
            'body' => "L'agence {$agency->name} a confirmé votre réservation pour le véhicule {$reservation->vehicle->brand} {$reservation->vehicle->model}.",
            'url' => route('reservation.show', $reservation->id),
        ]), ['event' => 'reservation_confirmed']);

        return redirect()
            ->route('agency.reservations.index', $agency->slug)
            ->with('success', 'Reservation confirmee avec succes.');
    }

    public function cancelReservation(string $slug, Reservation $reservation): RedirectResponse
    {
        $agency = $this->resolveOwnedAgency($slug);
        $this->ensureAgencyReservation($agency, $reservation);

        $updated = app(ReservationTransitionService::class)->cancel($reservation, 'agency');
        if (! $updated) {
            return redirect()
                ->route('agency.dashboard', $agency->slug)
                ->with('error', 'Cette reservation ne peut pas etre annulee.');
        }

        $reservation = $updated;

        // Notification au client
        SafeNotifier::send($reservation->client, new AppNotification([
            'subject' => 'Réservation annulée ❌',
            'body' => "L'agence {$agency->name} a dû annuler votre réservation pour le véhicule {$reservation->vehicle->brand}.",
            'url' => route('reservation.index'),
        ]), ['event' => 'reservation_cancelled_by_agency']);

        return redirect()
            ->route('agency.reservations.index', $agency->slug)
            ->with('success', 'Reservation annulee avec succes.');
    }

    private function resolveOwnedAgency(string $slug): Agency
    {
        $agency = Agency::where('slug', $slug)->firstOrFail();

        if ($agency->founder_id !== auth()->id()) {
            abort(403, 'Vous n\'etes pas autorise a acceder a ce tableau de bord.');
        }

        if ($agency->status !== 'active') {
            abort(403, 'Votre agence est actuellement inactive. Veuillez contacter le support.');
        }

        return $agency;
    }

    private function ensureAgencyReservation(Agency $agency, Reservation $reservation): void
    {
        abort_unless($reservation->agency_id === $agency->id, 403);
    }
}
