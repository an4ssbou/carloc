<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Models\Vehicle;
use App\Notifications\AppNotification;
use App\Support\SafeNotifier;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ReservationController extends Controller
{
    public function create(Request $request, Vehicle $vehicle): View
    {
        $this->ensureActiveAccount($request);
        $vehicle->loadMissing('agency');
        abort_unless($vehicle->agency && $vehicle->agency->isActive(), 404);
        $profileRequirements = $this->getReservationProfileRequirements($request);

        return view('reservations.create', compact('vehicle', 'profileRequirements'));
    }

    public function index(Request $request): View
    {
        $this->ensureActiveAccount($request);
        $reservations = Reservation::with('vehicle.agency')
            ->where('user_id', $request->user()->id)->latest()->paginate(10);

        return view('reservations.index', compact('reservations'));
    }

    public function show(Request $request, Reservation $reservation): View
    {
        $this->ensureOwner($request, $reservation);
        $reservation->loadMissing('vehicle.agency', 'client');

        return view('reservations.show', compact('reservation'));
    }

    public function contract(Request $request, Reservation $reservation): View
    {
        $this->ensureOwner($request, $reservation);
        // Old reservations have no immutable snapshot; never silently reconstruct one.
        $snapshot = $reservation->contract_snapshot;

        return view('reservations.contract', compact('reservation', 'snapshot'));
    }

    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $this->ensureActiveAccount($request);
        abort_unless($request->user()->hasVerifiedEmail(), 403);
        $requirements = $this->getReservationProfileRequirements($request);
        if (! $requirements['is_complete']) {
            throw ValidationException::withMessages(['profile' => $requirements['message']]);
        }

        // Keep validation outside a catch-all: Laravel returns 422 JSON or form errors.
        $validated = $request->validate([
            'vehicle_id' => ['required', 'integer', 'exists:vehicles,id'],
            'start_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after:start_date'],
        ]);
        $start = Carbon::parse($validated['start_date'])->startOfDay();
        $end = Carbon::parse($validated['end_date'])->startOfDay();
        $days = (int) $start->diffInDays($end);
        if ($days > 30) {
            throw ValidationException::withMessages(['end_date' => 'La durée maximale est de 30 jours.']);
        }

        $reservation = DB::transaction(function () use ($request, $validated, $start, $end, $days) {
            // All reservation writers in this journey serialize on this vehicle row.
            $vehicle = Vehicle::with('agency')->lockForUpdate()->findOrFail($validated['vehicle_id']);
            if (! $vehicle->agency || ! $vehicle->agency->isActive() || $vehicle->is_manually_unavailable) {
                throw ValidationException::withMessages(['vehicle_id' => 'Ce véhicule ou cette agence est indisponible.']);
            }
            if ($vehicle->reservations()->overlapping($start->toDateString(), $end->toDateString())->exists()) {
                throw ValidationException::withMessages(['start_date' => 'Voiture non disponible sur cette période. Les dates de retour sont également bloquées.']);
            }
            $user = $request->user();
            $price = $vehicle->getPriceForDuration($days);
            $total = $vehicle->calculateRentalTotal($days);

            return Reservation::create([
                'vehicle_id' => $vehicle->id, 'agency_id' => $vehicle->agency_id,
                'user_id' => $user->id, 'start_date' => $start, 'end_date' => $end,
                'price_applied' => $price, 'total_price' => $total, 'status' => 'pending',
                'payment_status' => 'not_simulated',
                'contract_snapshot' => [
                    'version' => 1, 'client_name' => $user->full_name, 'client_email' => $user->email,
                    'agency_name' => $vehicle->agency->name, 'agency_city' => $vehicle->agency->city,
                    'agency_address' => $vehicle->agency->address,
                    'vehicle_name' => $vehicle->brand.' '.$vehicle->model,
                    'registration_number' => $vehicle->registration_number,
                    'start_date' => $start->toDateString(), 'end_date' => $end->toDateString(),
                    'duration_days' => $days, 'price_applied' => number_format($price, 2, '.', ''),
                    'total_price' => number_format($total, 2, '.', ''), 'currency' => 'MAD',
                ],
            ]);
        }, 3);

        // Delivery is deliberately after commit; a notification failure cannot undo the booking.
        $reservation->loadMissing('agency.founder');
        SafeNotifier::send($reservation->agency?->founder, new AppNotification([
            'subject' => 'Nouvelle réservation',
            'body' => 'Une demande de réservation a été enregistrée : '.$reservation->reference.'. Aucun paiement bancaire effectué.',
            'url' => route('agency.reservations.index', $reservation->agency->slug),
        ]), ['event' => 'reservation_created_for_agency']);
        SafeNotifier::send($request->user(), new AppNotification([
            'subject' => 'Demande de réservation enregistrée',
            'body' => 'Votre demande '.$reservation->reference.' attend la validation de l’agence. Aucun paiement bancaire effectué.',
            'url' => route('reservation.show', $reservation),
        ]), ['event' => 'reservation_created_for_client']);

        $redirect = route('reservation.show', $reservation);
        if (! $request->expectsJson()) {
            return redirect($redirect)->with('success', 'Réservation enregistrée. Aucun paiement réel effectué.');
        }

        return response()->json([
            'success' => true, 'redirect_url' => $redirect,
            'reservation' => [
                'id' => $reservation->id, 'reference' => $reservation->reference,
                'days' => $reservation->duration_days, 'total_price' => (float) $reservation->total_price,
                'start_date' => $reservation->start_date->toDateString(), 'end_date' => $reservation->end_date->toDateString(),
            ],
        ]);
    }

    public function cancel(Request $request, Reservation $reservation): RedirectResponse
    {
        $this->ensureOwner($request, $reservation);
        $cancelled = DB::transaction(function () use ($reservation) {
            $locked = Reservation::lockForUpdate()->findOrFail($reservation->id);
            if (! $locked->canBeCancelledByClient()) {
                return false;
            }
            $locked->update(['status' => 'cancelled', 'cancelled_at' => now(), 'cancelled_by' => 'client']);

            return true;
        }, 3);

        if ($cancelled) {
            $reservation->loadMissing('agency.founder');
            if ($reservation->agency) {
                SafeNotifier::send($reservation->agency->founder, new AppNotification([
                    'subject' => 'Réservation annulée par le client',
                    'body' => 'Le client a annulé la réservation '.$reservation->reference.'. Aucun remboursement bancaire à effectuer dans cette démonstration.',
                    'url' => route('agency.reservations.index', $reservation->agency->slug),
                ]), ['event' => 'reservation_cancelled_by_client']);
            }
        }

        return redirect()->route('reservation.show', $reservation)->with(
            $cancelled ? 'success' : 'error',
            $cancelled ? 'Réservation annulée. Aucun remboursement bancaire : seuls des paiements simulés sont proposés.'
                : 'Cette réservation ne peut plus être annulée.'
        );
    }

    private function ensureActiveAccount(Request $request): void
    {
        abort_unless($request->user()?->is_active, 403);
    }

    private function ensureOwner(Request $request, Reservation $reservation): void
    {
        $this->ensureActiveAccount($request);
        abort_unless($reservation->user_id === $request->user()->id, 403);
    }

    private function getReservationProfileRequirements(Request $request): array
    {
        $user = $request->user();
        $missing = [];
        if (blank($user->cin_number)) {
            $missing[] = 'Numéro CIN';
        }
        if (blank($user->cin_photo)) {
            $missing[] = 'Photo CIN';
        }
        if (blank($user->license_photo)) {
            $missing[] = 'Photo du permis';
        }

        return [
            'is_complete' => $missing === [], 'missing_fields' => $missing,
            'message' => $missing ? 'Complétez votre profil : '.implode(', ', $missing).'.' : null,
        ];
    }
}
