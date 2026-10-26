<x-app-layout :title="$agency ? $agency->name . ' - Véhicules' : 'Véhicules'">
    @php
        $catalogUrl = $agency ? route('agency.vehicles', $agency->slug) : route('vehicles.index');
        $totalVehicles = $vehicles->total() + ($featuredVehicle ? 1 : 0);
        $shownVehicles = $vehicles->count() + ($featuredVehicle ? 1 : 0);
        $hasFilters = request()->filled('search') || request()->filled('city') || request()->filled('transmission') || request()->filled('has_gps');
    @endphp
    <style>
        .vehicle-catalog { color: #171c1f; background: #f3f4f6; }
        .vehicle-catalog .catalog-hero, .vehicle-catalog .catalog-primary { background: #0d2137; color: white; }
        .vehicle-catalog .catalog-paper { background: white; }
        .vehicle-catalog .catalog-soft { background: #f0f4f8; }
        .vehicle-catalog .catalog-gold { color: #815500; }
        .vehicle-catalog .catalog-muted { color: #44474d; }
        .vehicle-catalog .catalog-field { min-width: 0; }
        .vehicle-catalog :is(a, button, input, select):focus-visible { outline: 3px solid #b47700; outline-offset: 3px; }
        .vehicle-catalog .catalog-card { border-left: 4px solid transparent; transition: transform .3s, box-shadow .3s, border-color .3s; }
        .vehicle-catalog .catalog-card:hover { border-left-color: #815500; transform: translateY(-4px); box-shadow: 0 20px 25px -5px rgb(13 33 55 / .12); }
        .vehicle-catalog .catalog-photo { background: #0d2137; aspect-ratio: 16 / 10; }
        .vehicle-catalog .catalog-photo img { transition: transform .5s; }
        .vehicle-catalog .catalog-card:hover .catalog-photo img { transform: scale(1.05); }
        @media (prefers-reduced-motion: reduce) { .vehicle-catalog .catalog-card, .vehicle-catalog .catalog-photo img { transition: none; } .vehicle-catalog .catalog-card:hover, .vehicle-catalog .catalog-card:hover .catalog-photo img { transform: none; } }
    </style>

    <div class="vehicle-catalog pt-16" data-testid="vehicle-catalog">
        <header class="catalog-hero py-16 px-6">
            <div class="max-w-7xl mx-auto text-center">
                <nav aria-label="Fil d’Ariane" class="flex justify-center mb-6 gap-2 text-sm font-medium text-white/70">
                    <a href="{{ route('home') }}" class="hover:text-white">Accueil</a><span aria-hidden="true">/</span>
                    @if($agency)
                        <a href="{{ route('agency.index') }}" class="hover:text-white">Agences</a><span aria-hidden="true">/</span>
                    @endif
                    <span aria-current="page">{{ $agency ? $agency->name : 'Véhicules' }}</span>
                </nav>
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">{{ $agency ? $agency->name : 'Nos véhicules à louer' }}</h1>
                <p class="text-lg text-outline-variant max-w-2xl mx-auto mb-10" style="color: #c4c6cd;">
                    {{ $agency ? "Retrouvez les véhicules proposés par {$agency->name} et choisissez votre prochaine location." : 'Découvrez les véhicules de nos agences partenaires à travers le Maroc et trouvez celui qui vous correspond.' }}
                </p>
                <div class="flex flex-wrap justify-center gap-4">
                    <div class="bg-white/10 border border-white/10 px-6 py-2 rounded-full flex items-center gap-2">
                        <span class="font-bold" style="color: #feb234;">{{ $totalVehicles }}</span>
                        <span class="text-sm uppercase tracking-wider font-semibold">{{ $totalVehicles === 1 ? 'Véhicule trouvé' : 'Véhicules trouvés' }}</span>
                    </div>
                    @if($agency)
                        <div class="bg-white/10 border border-white/10 px-6 py-2 rounded-full flex items-center gap-2 text-sm font-semibold">
                            <span class="material-symbols-outlined text-lg" aria-hidden="true" style="color: #feb234;">location_on</span>{{ $agency->city }}
                        </div>
                    @endif
                </div>
            </div>
        </header>

        <section aria-label="Rechercher un véhicule" class="max-w-7xl mx-auto px-6 -mt-8 relative z-10">
            <form id="vehicle-filters" method="GET" action="{{ $catalogUrl }}" class="catalog-paper rounded-xl shadow-xl p-4 md:p-2 border border-outline-variant/10">
                <div class="flex flex-col md:flex-row gap-2">
                    <div class="catalog-field catalog-soft flex-1 flex items-center px-4 rounded-lg">
                        <span class="material-symbols-outlined text-outline" aria-hidden="true">search</span>
                        <label for="vehicle-search" class="sr-only">Marque ou modèle</label>
                        <input id="vehicle-search" name="search" type="search" value="{{ request('search') }}" placeholder="Marque ou modèle…" class="min-w-0 w-full bg-transparent border-none focus:ring-0 py-3 px-3 text-sm">
                    </div>
                    @unless($agency)
                        <div class="catalog-field catalog-soft md:w-1/3 flex items-center px-4 rounded-lg">
                            <span class="material-symbols-outlined text-outline" aria-hidden="true">location_on</span>
                            <label for="vehicle-city" class="sr-only">Ville</label>
                            <select id="vehicle-city" name="city" class="min-w-0 w-full bg-transparent border-none focus:ring-0 py-3 px-3 text-sm">
                                <option value="">Toutes les villes</option>
                                @foreach($cities as $city)
                                    @php($cityName = is_array($city) ? ($city['city'] ?? '') : $city)
                                    <option value="{{ $cityName }}" @selected(request('city') === $cityName)>{{ $cityName }}</option>
                                @endforeach
                            </select>
                        </div>
                    @endunless
                    <button type="submit" class="gold-gradient text-white font-bold py-3 px-8 rounded-lg transition-transform active:scale-95 shadow-lg shadow-secondary/20">Rechercher</button>
                </div>
            </form>
        </section>

        <div class="max-w-7xl mx-auto px-6 py-12">
            @if(session('success'))
                <div role="status" class="mb-8 bg-green-100 border border-green-400 text-green-700 px-6 py-4 rounded-xl">{{ session('success') }}</div>
            @endif
            <div class="flex flex-col md:flex-row gap-8">
                <aside class="w-full md:w-1/4 min-w-0" aria-labelledby="vehicle-filter-heading">
                    <div class="catalog-paper md:sticky md:top-24 rounded-xl p-6 shadow-sm border border-outline-variant/10">
                        <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
                            <h2 id="vehicle-filter-heading" class="text-xs font-black uppercase tracking-widest">Filtrer les véhicules</h2>
                            <a href="{{ $catalogUrl }}" class="catalog-gold text-[11px] font-bold uppercase tracking-wider hover:underline">Réinitialiser</a>
                        </div>
                        <fieldset class="mb-8">
                            <legend class="text-sm font-bold mb-4">Transmission</legend>
                            <div class="space-y-3">
                                @foreach(['' => 'Toutes', 'manual' => 'Manuelle', 'automatic' => 'Automatique'] as $value => $label)
                                    <label class="flex items-center gap-3 text-sm catalog-muted cursor-pointer">
                                        <input form="vehicle-filters" type="radio" name="transmission" value="{{ $value }}" @checked((string) request('transmission', '') === $value) class="text-secondary border-outline-variant focus:ring-secondary/20">
                                        {{ $label }}
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>
                        <fieldset class="mb-8">
                            <legend class="text-sm font-bold mb-4">Équipements</legend>
                            <label class="flex items-center gap-3 text-sm catalog-muted cursor-pointer">
                                <input form="vehicle-filters" type="checkbox" name="has_gps" value="1" @checked(request('has_gps')) class="rounded text-secondary border-outline-variant focus:ring-secondary/20">Avec GPS
                            </label>
                        </fieldset>
                        <button form="vehicle-filters" type="submit" class="w-full py-2 px-4 rounded-full gold-gradient text-white text-xs font-bold shadow-md">Appliquer les filtres</button>
                    </div>
                </aside>

                <section class="w-full md:w-3/4 min-w-0" aria-labelledby="vehicle-results-heading">
                    <div class="flex flex-wrap justify-between items-center gap-3 mb-6">
                        <h2 id="vehicle-results-heading" class="text-sm font-bold text-outline uppercase tracking-wider">{{ $agency ? 'Les véhicules de l’agence' : 'Véhicules' }}</h2>
                        <p class="text-sm catalog-muted"><span class="font-bold">{{ $shownVehicles }}</span> sur {{ $totalVehicles }} véhicule(s)</p>
                    </div>
                    @if($totalVehicles === 0)
                        <div class="catalog-paper text-center py-16 px-6 rounded-xl shadow-sm border border-outline-variant/10">
                            <span class="material-symbols-outlined text-5xl catalog-gold mb-4" aria-hidden="true">directions_car</span>
                            <h3 class="text-xl font-extrabold mb-2">Aucun véhicule trouvé</h3>
                            <p class="text-sm catalog-muted mb-6">{{ $hasFilters ? 'Essayez une autre recherche ou retirez quelques filtres.' : 'Aucun véhicule n’est disponible pour le moment. Revenez consulter nos offres.' }}</p>
                            <a href="{{ $catalogUrl }}" class="catalog-primary inline-block rounded font-bold text-sm px-6 py-3">Voir tous les véhicules</a>
                        </div>
                    @else
                        @if($agency && $featuredVehicle)
                            <div class="mb-6"><x-vehicle-catalog-card :vehicle="$featuredVehicle" :featured="true" /></div>
                        @endif
                        @if($vehicles->isNotEmpty())
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                @foreach($vehicles as $vehicle)
                                    <x-vehicle-catalog-card :vehicle="$vehicle" />
                                @endforeach
                            </div>
                        @endif
                        @if($vehicles->hasPages())
                            <nav aria-label="Pagination des véhicules" class="mt-12">{{ $vehicles->links() }}</nav>
                        @endif
                    @endif
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
