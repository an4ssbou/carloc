<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\GpsPositionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GpsIngestionController extends Controller
{
    public function store(Request $request, Vehicle $vehicle, GpsPositionService $gps): JsonResponse
    {
        $tracking = DB::transaction(function () use ($request, $vehicle, $gps) {
            $locked = Vehicle::lockForUpdate()->findOrFail($vehicle->id);
            $bearer = $request->bearerToken();
            abort_unless($bearer && $locked->gps_token_hash
                && hash_equals($locked->gps_token_hash, hash('sha256', $bearer)), 401);
            $locked->load('agency.founder');
            abort_unless($locked->agency?->isActive() && $locked->agency->founder?->is_active
                && $locked->agency->founder->hasVerifiedEmail(), 403);
            abort_unless($locked->has_gps, 409, 'GPS désactivé.');
            $gps->write($locked, $gps->validatePosition($request));

            return $gps->tracking($locked);
        }, 3);

        return response()->json($tracking)->header('Cache-Control', 'no-store');
    }
}
