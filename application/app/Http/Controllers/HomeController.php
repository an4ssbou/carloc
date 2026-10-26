<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;
use App\Models\Vehicle;
use App\Models\Agency;
use App\Models\Reservation;

class HomeController extends Controller
{
    public function index(): View
    {
        $total_vehicles = Vehicle::whereHas('agency', function ($query) {
            $query->active();
        })->count();

        $cities = json_decode(file_get_contents(app_path('ma.json')), true);

        $agencies = Agency::active()
                          ->selectRaw('COUNT(id) as total, COUNT(DISTINCT city) as cities')
                          ->first();

        $topAgencies = Agency::active()
                            ->withCount(['reservations' => function ($query) {
                                $query->whereIn('status', ['confirmed', 'completed']);
                            }])
                            ->withCount('vehicles')
                            ->orderBy('reservations_count', 'desc')
                            ->limit(3)
                            ->get();
        
        $topVehicles = Vehicle::whereHas('agency', function ($query) {
                                $query->active();
                            })
                            ->available()
                            ->withCount(['reservations' => function ($query) {
                                $query->whereIn('status', ['confirmed', 'completed']);
                            }])
                            ->with(['agency', 'primaryPhoto'])
                            ->orderByDesc('reservations_count')
                            ->take(3)
                            ->get();

       

    return view('home', ['cities' => $cities,
            'nbr_cities'     => $agencies->cities,
            'total_vehicles' => $total_vehicles,
            'nbr_agencies'   => $agencies->total,
            'top_agencies'   => $topAgencies,
            'top_vehicles'   => $topVehicles]);
    }

    public function contact(): View
    {
        return view('contact');
    }
}
