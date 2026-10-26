<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    public function pay(Request $request): JsonResponse
    {
        return response()->json([
            'success' => false,
            'error' => 'Paiement bancaire désactivé. Utilisez la simulation liée à votre réservation, sans numéro de carte.',
        ], 409);
    }

    public function simulate(Request $request, Reservation $reservation): JsonResponse|RedirectResponse
    {
        abort_unless($request->user()?->is_active && $request->user()->hasVerifiedEmail(), 403);
        abort_unless($reservation->user_id === $request->user()->id, 403);
        abort_unless(config('payments.demo_enabled'), 404);
        $reservation = DB::transaction(function () use ($reservation) {
            $locked = Reservation::lockForUpdate()->findOrFail($reservation->id);
            if (! $locked->isActive()) {
                throw ValidationException::withMessages(['payment' => 'La simulation exige une réservation active.']);
            }
            if ($locked->payment_status !== 'simulated') {
                $locked->update([
                    'payment_status' => 'simulated', 'payment_reference' => 'DEMO-'.Str::uuid(),
                    'payment_simulated_at' => now(),
                ]);
            }

            return $locked;
        }, 3);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'simulated' => true, 'reference' => $reservation->payment_reference]);
        }

        return redirect()->route('reservation.show', $reservation)
            ->with('success', 'Paiement simulé — aucun débit, aucune carte bancaire utilisée.');
    }
}
