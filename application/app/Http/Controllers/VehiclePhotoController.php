<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VehiclePhotoController extends Controller
{
    protected function ensureVehicleOwner(Vehicle $vehicle): void
    {
        $vehicle->loadMissing('agency');

        if (!$vehicle->agency || $vehicle->agency->founder_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à gérer les photos de ce véhicule.');
        }

        if (! $vehicle->agency->isActive()) {
            abort(403, 'Votre agence doit être active pour gérer ses véhicules.');
        }
    }

    protected function ensurePhotoBelongsToVehicle(Vehicle $vehicle, VehiclePhoto $photo): void
    {
        if ($photo->vehicle_id !== $vehicle->id) {
            abort(404);
        }
    }

    // Ajouter des photos à un véhicule
    public function store(Request $request, Vehicle $vehicle)
    {
        $this->ensureVehicleOwner($vehicle);

        $request->validate([
            'photos'   => 'required|array|max:10',
            'photos.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        foreach ($request->file('photos') as $index => $photo) {
            $path = $photo->store('vehicles', 'public');

            VehiclePhoto::create([
                'vehicle_id' => $vehicle->id,
                'path'       => $path,
                'is_primary' => $index === 0 && !$vehicle->primaryPhoto()->exists(),
                'order'      => $vehicle->photos()->count() + $index,
            ]);
        }

        return back()->with('success', 'Photos ajoutées avec succès !');
    }

    // Définir une photo comme principale
    public function setPrimary(Vehicle $vehicle, VehiclePhoto $photo)
    {
        $this->ensureVehicleOwner($vehicle);
        $this->ensurePhotoBelongsToVehicle($vehicle, $photo);

        $vehicle->photos()->update(['is_primary' => false]);
        $photo->update(['is_primary' => true]);

        return back()->with('success', 'Photo principale mise à jour !');
    }

    // Supprimer une photo
    public function destroy(Vehicle $vehicle, VehiclePhoto $photo)
    {
        $this->ensureVehicleOwner($vehicle);
        $this->ensurePhotoBelongsToVehicle($vehicle, $photo);

        Storage::disk('public')->delete($photo->path);
        $photo->delete();

        if ($photo->is_primary) {
            $next = $vehicle->photos()->first();
            if ($next) {
                $next->update(['is_primary' => true]);
            }
        }

        return back()->with('success', 'Photo supprimée avec succès !');
    }
}
