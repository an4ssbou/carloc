<?php

namespace App\Services;

use App\Models\Vehicle;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class GpsPositionService
{
    public function authorizeOwner(Request $request, Vehicle $vehicle): void
    {
        $vehicle->loadMissing('agency.founder');
        $owner = $request->user();
        abort_unless($owner && $owner->is_active && $owner->hasVerifiedEmail()
            && $vehicle->agency?->isActive() && $vehicle->agency->founder_id === $owner->id, 403);
    }

    public function simulationEnabled(): bool
    {
        return app()->environment('local', 'testing') && (bool) config('gps.allow_simulation');
    }

    public function tracking(Vehicle $vehicle): array
    {
        $position = null;
        if ($vehicle->has_gps && $vehicle->gps_recorded_at && $vehicle->gps_received_at
            && $vehicle->gps_latitude !== null && $vehicle->gps_longitude !== null
            && in_array($vehicle->gps_source, ['device', 'simulation'], true)) {
            $position = [
                'latitude' => (float) $vehicle->gps_latitude,
                'longitude' => (float) $vehicle->gps_longitude,
                'recorded_at' => $vehicle->gps_recorded_at->utc()->toISOString(),
                'received_at' => $vehicle->gps_received_at->utc()->toISOString(),
                'source' => $vehicle->gps_source,
            ];
        }
        $status = ! $vehicle->has_gps ? 'disabled' : ($position === null ? 'no_signal'
            : ($vehicle->gps_recorded_at->lt(now()->subSeconds(60)) ? 'stale' : 'recent'));

        return [
            'vehicle' => ['uuid' => $vehicle->uuid, 'name' => $vehicle->brand.' '.$vehicle->model, 'has_gps' => $vehicle->has_gps],
            'position' => $position,
            'status' => $status,
            'token_configured' => filled($vehicle->gps_token_hash),
            'simulation_allowed' => $this->simulationEnabled() && $vehicle->has_gps && blank($vehicle->gps_token_hash),
            'poll_interval_seconds' => 5,
            'stale_after_seconds' => 60,
            'server_time' => now()->utc()->toISOString(),
        ];
    }

    public function validatePosition(Request $request): array
    {
        $data = $request->validate([
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'recorded_at' => ['required', 'string', 'date', 'regex:/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,6})?(?:Z|[+-]\d{2}:\d{2})$/'],
            'source' => ['sometimes', 'in:device,simulation'],
        ]);
        $recorded = CarbonImmutable::parse($data['recorded_at'])->utc();
        if ($recorded->lt(now()->subHours(24)) || $recorded->gt(now()->addSeconds(30))) {
            throw ValidationException::withMessages(['recorded_at' => 'La mesure doit dater de moins de 24 h et ne pas dépasser 30 s dans le futur.']);
        }

        return ['latitude' => (float) $data['latitude'], 'longitude' => (float) $data['longitude'],
            'recorded_at' => $recorded, 'source' => $data['source'] ?? 'device'];
    }

    // Caller owns the vehicle row lock and authorization checks throughout this write.
    public function write(Vehicle $vehicle, array $data): void
    {
        abort_unless($vehicle->has_gps, 409, 'GPS désactivé.');
        if ($vehicle->gps_recorded_at) {
            abort_if($data['recorded_at']->lt($vehicle->gps_recorded_at), 409, 'Mesure antérieure à la dernière position.');
            if ($data['recorded_at']->equalTo($vehicle->gps_recorded_at)) {
                $same = round($data['latitude'], 7) === round((float) $vehicle->gps_latitude, 7)
                    && round($data['longitude'], 7) === round((float) $vehicle->gps_longitude, 7)
                    && $data['source'] === $vehicle->gps_source;
                abort_unless($same, 409, 'Une mesure différente existe déjà à cette date.');

                return;
            }
        }
        $vehicle->forceFill([
            'gps_latitude' => $data['latitude'], 'gps_longitude' => $data['longitude'],
            'gps_recorded_at' => $data['recorded_at'], 'gps_received_at' => now()->utc(), 'gps_source' => $data['source'],
        ])->save();
    }
}
