<?php

namespace App\Services;

use App\Models\Reservation;
use Illuminate\Support\Facades\DB;

class ReservationTransitionService
{
    public function confirm(Reservation $reservation): ?Reservation
    {
        return DB::transaction(function () use ($reservation) {
            $locked = Reservation::lockForUpdate()->findOrFail($reservation->id);
            if (! $locked->canBeConfirmedByAgency()) {
                return null;
            }
            $locked->update(['status' => 'confirmed', 'confirmed_at' => now(), 'cancelled_at' => null, 'cancelled_by' => null]);

            return $locked;
        }, 3);
    }

    public function cancel(Reservation $reservation, string $actor): ?Reservation
    {
        return DB::transaction(function () use ($reservation, $actor) {
            $locked = Reservation::lockForUpdate()->findOrFail($reservation->id);
            if (! in_array($locked->status, ['pending', 'confirmed'], true)) {
                return null;
            }
            $locked->update(['status' => 'cancelled', 'cancelled_at' => now(), 'cancelled_by' => $actor]);

            return $locked;
        }, 3);
    }
}
