<?php

namespace App\Observers;

use App\Models\Reservation;

class ReservationObserver
{
    public function created(Reservation $reservation): void
    {
        $reservation->agency->increment('total_reservations');

        VehicleObserver::updateVehicleStatus($reservation->vehicle);
    }

    public function updated(Reservation $reservation): void
    {
        if ($reservation->wasChanged('status')) {
            VehicleObserver::updateVehicleStatus($reservation->vehicle);
        }
    }
}