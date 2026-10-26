<?php

namespace App\Http\Controllers;

use App\Http\Requests\VehicleRequest;
use App\Models\Agency;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\VehiclePhoto;
use App\Notifications\AppNotification;
use App\Support\SafeNotifier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class VehicleController extends Controller
{
    protected function applyVehicleFilters($query, Request $request)
    {
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('brand', 'like', '%'.$request->search.'%')
                    ->orWhere('model', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('city')) {
            $query->whereHas('agency', function ($q) use ($request) {
                $q->where('city', $request->city);
            });
        }

        if ($request->filled('transmission')) {
            $query->where('transmission', $request->transmission);
        }

        if ($request->filled('has_gps')) {
            $query->where('has_gps', true);
        }

        return $query;
    }

    protected function ensureSingleFeaturedVehicle(int $agencyId, ?int $exceptVehicleId = null): void
    {
        Vehicle::query()
            ->where('agency_id', $agencyId)
            ->when($exceptVehicleId, fn ($query) => $query->whereKeyNot($exceptVehicleId))
            ->where('is_featured', true)
            ->update(['is_featured' => false]);
    }

    /**
     * Vérifie que l'utilisateur est propriétaire du véhicule
     */
    protected function ensureVehicleOwner(Vehicle $vehicle): void
    {
        $vehicle->loadMissing('agency');

        if (! $vehicle->agency || $vehicle->agency->founder_id !== auth()->id()) {
            abort(403, 'Vous n\'êtes pas autorisé à gérer ce véhicule.');
        }

        if (! $vehicle->agency->isActive()) {
            abort(403, 'Votre agence doit être active pour gérer ses véhicules.');
        }
    }

    /**
     * Liste des véhicules (avec filtres + slug agence)
     */
    public function index(Request $request, $slug = null)
    {
        $query = Vehicle::with(['agency', 'primaryPhoto'])
            ->whereHas('agency', fn ($agencyQuery) => $agencyQuery->active())
            ->available();

        $this->applyVehicleFilters($query, $request);

        // 🏢 Filtre par agence via slug
        $agency = null;
        $featuredVehicle = null;
        if ($slug) {
            $agency = Agency::where('slug', $slug)->where('status', 'active')->firstOrFail();
            $query->where('agency_id', $agency->id);

            $featuredVehicle = Vehicle::with(['agency', 'primaryPhoto'])
                ->where('agency_id', $agency->id)
                ->available()
                ->where('is_featured', true);

            $this->applyVehicleFilters($featuredVehicle, $request);

            $featuredVehicle = $featuredVehicle->first();

            if ($featuredVehicle) {
                $query->whereKeyNot($featuredVehicle->id);
            }
        }

        $vehicles = $query->paginate(12)->withQueryString();

        $cities = Cache::remember('ma_city_names_v2', 60 * 60 * 24, function () {
            return collect(json_decode(file_get_contents(app_path('ma.json')), true))
                ->map(function ($city) {
                    if (is_array($city)) {
                        return $city['city'] ?? null;
                    }

                    return is_string($city) ? $city : null;
                })
                ->filter(fn ($city) => is_string($city) && $city !== '')
                ->unique()
                ->sort()
                ->values()
                ->all();
        });

        return view('vehicles.index', compact('vehicles', 'cities', 'agency', 'featuredVehicle'));
    }

    public function create()
    {
        $agency = Agency::where('founder_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (! $agency) {
            return redirect()->route('vehicles.index')
                ->with('error', 'Vous devez avoir une agence active avant d\'ajouter un véhicule.');
        }

        return view('vehicles.create', compact('agency'));
    }

    public function store(VehicleRequest $request)
    {
        $agency = Agency::where('founder_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (! $agency) {
            return redirect()->route('vehicles.index')
                ->with('error', 'Vous devez avoir une agence active avant d\'ajouter un véhicule.');
        }

        $validated = $request->validated();

        $validated['agency_id'] = $agency->id;
        $validated['has_gps'] = $request->boolean('has_gps');
        $validated['is_featured'] = $request->boolean('is_featured');

        $vehicle = DB::transaction(function () use ($validated, $request, $agency) {
            if ($validated['is_featured']) {
                $this->ensureSingleFeaturedVehicle($agency->id);
            }

            $vehicle = Vehicle::create($validated);

            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    $path = $photo->store('vehicles', 'public');

                    VehiclePhoto::create([
                        'vehicle_id' => $vehicle->id,
                        'path' => $path,
                        'is_primary' => $index === 0,
                        'order' => $index,
                    ]);
                }
            }

            //  la notification envoyer a l Administrarteur
            $superAdmin = User::where('role', 'super_admin')->first();
            if ($superAdmin) {
                SafeNotifier::send($superAdmin, new AppNotification([
                    'subject' => 'Nouveau véhicule : '.$vehicle->brand,
                    'body' => "L'agence {$agency->name} a ajouté un(e) {$vehicle->brand} {$vehicle->model}.",
                    'url' => route('vehicles.show', $vehicle->id),
                ]), ['event' => 'vehicle_created']);
            }

            return $vehicle;
        });

        return redirect()->route('agency.vehicles.manage')
            ->with('success', 'Véhicule ajouté avec succès !');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['agency', 'photos', 'primaryPhoto']);

        if (! $vehicle->agency || ! $vehicle->agency->isActive() || ! $vehicle->is_available) {
            abort(404);
        }

        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        $this->ensureVehicleOwner($vehicle);

        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(VehicleRequest $request, Vehicle $vehicle)
    {
        $this->ensureVehicleOwner($vehicle);

        $validated = $request->validated();

        $validated['has_gps'] = $request->boolean('has_gps');
        $validated['is_featured'] = $request->boolean('is_featured');
        $deletePhotoIds = collect($validated['delete_photo_ids'] ?? [])
            ->map(fn ($photoId) => (int) $photoId)
            ->all();

        // Retirer photos du tableau
        $photos = $validated['photos'] ?? null;
        unset($validated['delete_photo_ids']);
        unset($validated['photos']);

        DB::transaction(function () use ($validated, $request, $vehicle, $deletePhotoIds) {
            $vehicle = Vehicle::lockForUpdate()->findOrFail($vehicle->id);
            $this->ensureVehicleOwner($vehicle);
            if (($validated['is_featured'] ?? false) === true) {
                $this->ensureSingleFeaturedVehicle($vehicle->agency_id, $vehicle->id);
            }

            $vehicle->update($validated);

            if (! empty($deletePhotoIds)) {
                $photosToDelete = $vehicle->photos()
                    ->whereIn('id', $deletePhotoIds)
                    ->get();

                $deletedPrimary = $photosToDelete->contains('is_primary', true);

                foreach ($photosToDelete as $photo) {
                    Storage::disk('public')->delete($photo->path);
                    $photo->delete();
                }

                if ($deletedPrimary && ! $vehicle->photos()->where('is_primary', true)->exists()) {
                    $nextPhoto = $vehicle->photos()->orderBy('order')->first();

                    if ($nextPhoto) {
                        $nextPhoto->update(['is_primary' => true]);
                    }
                }
            }

            // Upload nouvelles photos
            if ($request->hasFile('photos')) {
                foreach ($request->file('photos') as $index => $photo) {
                    if (! $photo) {
                        continue;
                    }

                    $path = $photo->store('vehicles', 'public');

                    VehiclePhoto::create([
                        'vehicle_id' => $vehicle->id,
                        'path' => $path,
                        'is_primary' => $index === 0 && ! $vehicle->primaryPhoto()->exists(),
                        'order' => $vehicle->photos()->count() + $index,
                    ]);
                }
            }
        });

        // --- AJOUT DE LA NOTIFICATION  ---
        $vehicle->load('agency');
        $superAdmin = User::where('role', 'super_admin')->first();
        if ($superAdmin) {
            SafeNotifier::send($superAdmin, new AppNotification([
                'subject' => 'Véhicule modifié : '.$vehicle->brand,
                'body' => "L'agence {$vehicle->agency->name} a mis à jour le véhicule {$vehicle->brand} {$vehicle->model}.",
                'url' => route('vehicles.show', $vehicle->id),
            ]), ['event' => 'vehicle_updated']);
        }

        return redirect()->route('agency.vehicles.manage')
            ->with('success', 'Véhicule modifié avec succès !');
    }

    public function destroy(Vehicle $vehicle)
    {
        $this->ensureVehicleOwner($vehicle);

        // on note le nom AVANT de supprimer
        $agencyName = $vehicle->agency->name;
        $vehicleInfo = "{$vehicle->brand} {$vehicle->model}";

        $vehicle->delete();

        //  // --- AJOUT DE LA NOTIFICATION  --- envoyer a l Administrarteur
        $superAdmin = User::where('role', 'super_admin')->first();
        if ($superAdmin) {
            SafeNotifier::send($superAdmin, new AppNotification([
                'subject' => 'Véhicule supprimé',
                'body' => "L'agence {$agencyName} a supprimé le véhicule {$vehicleInfo}.",
                'url' => route('agency.vehicles.manage'),
            ]), ['event' => 'vehicle_deleted']);
        }

        return redirect()->route('agency.vehicles.manage')
            ->with('success', 'Véhicule supprimé avec succès !');
    }

    /**
     * Véhicules de l'agence connectée
     */
    public function agencyVehicles(Request $request)
    {
        $agency = Agency::where('founder_id', auth()->id())
            ->where('status', 'active')
            ->first();

        if (! $agency) {
            return redirect()->route('vehicles.index')
                ->with('error', 'Vous devez avoir une agence active pour gérer vos véhicules.');
        }

        $baseVehiclesQuery = $agency->vehicles();
        $fleetTotal = (clone $baseVehiclesQuery)->count();
        $availableVehicles = (clone $baseVehiclesQuery)->where('status', 'available')->count();
        $unavailableVehicles = (clone $baseVehiclesQuery)->where('status', 'unavailable')->count();
        $featuredVehicles = (clone $baseVehiclesQuery)->where('is_featured', true)->count();
        $gpsVehicles = (clone $baseVehiclesQuery)->where('has_gps', true)->count();
        $averagePricePerDay = (float) ((clone $baseVehiclesQuery)->avg('price_per_day') ?? 0);
        $pendingReservations = $agency->reservations()->where('status', 'pending')->count();

        $vehiclesQuery = $agency->vehicles()->with('primaryPhoto');

        if ($request->filled('search')) {
            $vehiclesQuery->where(function ($query) use ($request) {
                $query->where('brand', 'like', '%'.$request->search.'%')
                    ->orWhere('model', 'like', '%'.$request->search.'%')
                    ->orWhere('registration_number', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->filled('status')) {
            $vehiclesQuery->where('status', $request->status);
        }

        if ($request->filled('transmission')) {
            $vehiclesQuery->where('transmission', $request->transmission);
        }

        if ($request->filled('featured')) {
            $vehiclesQuery->where('is_featured', $request->boolean('featured'));
        }

        if ($request->filled('has_gps')) {
            $vehiclesQuery->where('has_gps', true);
        }

        $vehicles = $vehiclesQuery->paginate(12)->withQueryString();

        return view('agency.vehicles', compact(
            'vehicles',
            'agency',
            'fleetTotal',
            'pendingReservations',
            'availableVehicles',
            'unavailableVehicles',
            'featuredVehicles',
            'gpsVehicles',
            'averagePricePerDay',
        ));
    }
}
