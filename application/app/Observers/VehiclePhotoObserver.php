<?php

namespace App\Observers;

use App\Models\VehiclePhoto;

class VehiclePhotoObserver
{
    public function saving(VehiclePhoto $photo): void
    {
        if (! $photo->is_primary) {
            return;
        }

        VehiclePhoto::query()
            ->where('vehicle_id', $photo->vehicle_id)
            ->when($photo->exists, fn ($query) => $query->whereKeyNot($photo->id))
            ->where('is_primary', true)
            ->update(['is_primary' => false]);
    }
}
