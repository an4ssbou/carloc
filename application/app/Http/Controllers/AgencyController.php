<?php

namespace App\Http\Controllers;

use App\Http\Requests\AgencyStoreRequest;
use App\Http\Requests\AgencyUpdateRequest;
use App\Models\Agency;
use App\Models\Vehicle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AgencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $cities = Cache::remember('ma_cities', 60 * 60 * 24, function () {
            return json_decode(file_get_contents(app_path('ma.json')), true);
        });

        $total_vehicles = Vehicle::whereHas('agency', function ($query) {
            $query->active();
        })->count();

        $agenciesByCity = Agency::active()
            ->select('city', DB::raw('COUNT(id) as total'))
            ->groupBy('city')
            ->get();

        $nbr_cities = $agenciesByCity->count();

        $agencies = Agency::active()
            ->withCount('vehicles')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->city, function ($query, $city) {
                $query->where('city', $city);
            })
            ->paginate(4)
            ->withQueryString();

        return view('agency.index', [
            'agencies' => $agencies,
            'cities' => $cities,
            'nbr_cities' => $nbr_cities,
            'total_vehicles' => $total_vehicles,
            'agenciesByCity' => $agenciesByCity,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $agency = Agency::where('founder_id', auth()->id())->first();

        if ($agency) {
            abort(403, 'You already have an agency. You cannot create another one.');
        }

        $cities = json_decode(file_get_contents(app_path('ma.json')), true);

        return view('agency.create', [
            'cities' => $cities,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AgencyStoreRequest $request): RedirectResponse
    {
        $existingAgency = Agency::where('founder_id', auth()->id())->first();

        if ($existingAgency) {
            abort(403, 'You already have an agency. You cannot create another one.');
        }

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $name = $request->name;
        $city = $request->city;
        $address = $request->address;
        $phone = $request->phone;
        $email = $request->email;
        $description = $request->description;

        Agency::create([
            'name' => $name,
            'city' => $city,
            'address' => $address,
            'phone' => $phone,
            'email' => $email,
            'description' => $description,
            'logo' => $data['logo'] ?? null,
            'founder_id' => auth()->id(),
        ]);

        return Redirect::route('home')->with('status', 'success')->with('message', 'Your agency has been created successfully! Please wait for admin approval before listing your vehicles.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $slug) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $slug)
    {
        $cities = Cache::remember('ma_cities', 60 * 60 * 24, function () {
            return json_decode(file_get_contents(app_path('ma.json')), true);
        });
        $agency = Agency::where('slug', $slug)
            ->where('founder_id', auth()->id())
            ->firstOrFail();

        return view('agency.edit', [
            'agency' => $agency,
            'cities' => $cities,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AgencyUpdateRequest $request, string $slug): RedirectResponse
    {
        $agency = Agency::where('slug', $slug)
            ->where('founder_id', auth()->id())
            ->firstOrFail();

        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('logos', 'public');
        }

        $agency->name = $request->name;
        $agency->city = $request->city;
        $agency->address = $request->address;
        $agency->phone = $request->phone;
        $agency->email = $request->email;
        $agency->logo = $data['logo'] ?? $agency->logo;
        $agency->description = $request->description;
        $agency->save();

        return Redirect::route('home')->with('success', 'Agency modified succefully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('agencyDeletion', [
            'password' => ['required', 'current_password'],
        ]);
        $user = Auth::user();
        $agency = Agency::where('founder_id', $user->id)->first();

        if ($agency) {
            $agency->delete();
        }

        return Redirect::to('/');
    }
}
