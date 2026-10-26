<?php

namespace App\Observers;

use App\Models\Vehicle;

class VehicleObserver
{
    public function saved(Vehicle $vehicle): void
    {
        if ($vehicle->is_featured) {
            Vehicle::query()
                ->where('agency_id', $vehicle->agency_id)
                ->whereKeyNot($vehicle->id)
                ->where('is_featured', true)
                ->update(['is_featured' => false]);
        }
    }

    public static function syncStatuses(): void
    {
        Vehicle::query()
            ->with(['agency', 'reservations'])
            ->chunkById(100, function ($vehicles): void {
                foreach ($vehicles as $vehicle) {
                    self::updateVehicleStatus($vehicle);
                }
            });
    }

    public static function updateVehicleStatus(Vehicle $vehicle): void
    {
        $hasActiveReservation = $vehicle->activeReservations()
            ->where('start_date', '<=', now())
            ->where('end_date',   '>=', now())
            ->exists();

        $newStatus = ($hasActiveReservation || $vehicle->is_manually_unavailable)
            ? 'unavailable'
            : 'available';

        $vehicle->updateQuietly(['status' => $newStatus]);
    }
}
