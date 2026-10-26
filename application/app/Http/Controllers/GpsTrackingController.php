<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\GpsPositionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class GpsTrackingController extends Controller
{
    public function show(Request $request, Vehicle $vehicle, GpsPositionService $gps): Response
    {
        $gps->authorizeOwner($request, $vehicle);

        return response()->view('agency.gps', [
            'vehicle' => $vehicle, 'agency' => $vehicle->agency, 'initialTracking' => $gps->tracking($vehicle),
        ])->header('Cache-Control', 'no-store');
    }

    public function position(Request $request, Vehicle $vehicle, GpsPositionService $gps): JsonResponse
    {
        $gps->authorizeOwner($request, $vehicle);

        return response()->json($gps->tracking($vehicle))->header('Cache-Control', 'no-store');
    }

    public function token(Request $request, Vehicle $vehicle, GpsPositionService $gps): JsonResponse
    {
        $token = DB::transaction(function () use ($request, $vehicle, $gps) {
            $locked = Vehicle::lockForUpdate()->findOrFail($vehicle->id);
            $gps->authorizeOwner($request, $locked);
            abort_unless($locked->has_gps, 409, 'GPS désactivé.');
            $token = bin2hex(random_bytes(32));
            $locked->forceFill(['gps_token_hash' => hash('sha256', $token)])->save();

            return $token;
        }, 3);

        return response()->json(['token' => $token])->header('Cache-Control', 'no-store');
    }

    public function revoke(Request $request, Vehicle $vehicle, GpsPositionService $gps): JsonResponse
    {
        $tracking = DB::transaction(function () use ($request, $vehicle, $gps) {
            $locked = Vehicle::lockForUpdate()->findOrFail($vehicle->id);
            $gps->authorizeOwner($request, $locked);
            abort_unless($locked->has_gps, 409, 'GPS désactivé.');
            $locked->forceFill(['gps_token_hash' => null])->save();

            return $gps->tracking($locked);
        }, 3);

        return response()->json($tracking)->header('Cache-Control', 'no-store');
    }

    public function simulate(Request $request, Vehicle $vehicle, GpsPositionService $gps): JsonResponse
    {
        $gps->authorizeOwner($request, $vehicle);
        abort_unless($vehicle->has_gps, 409, 'GPS désactivé.');
        abort_unless($gps->simulationEnabled(), 403, 'Simulation désactivée.');
        $data = $gps->validatePosition($request);
        $data['source'] = 'simulation';
        $tracking = DB::transaction(function () use ($request, $vehicle, $gps, $data) {
            $locked = Vehicle::lockForUpdate()->findOrFail($vehicle->id);
            $gps->authorizeOwner($request, $locked);
            abort_unless($locked->has_gps, 409, 'GPS désactivé.');
            abort_if(filled($locked->gps_token_hash), 409, 'Révoquez la clé appareil avant une simulation.');
            $gps->write($locked, $data);

            return $gps->tracking($locked);
        }, 3);

        return response()->json($tracking)->header('Cache-Control', 'no-store');
    }
}
