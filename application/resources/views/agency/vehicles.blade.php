<x-agency-dashboard-layout
    :agency="$agency"
    page-title="Agency Vehicles"
    page-heading="Mes Vehicules"
    page-breadcrumb="Vehicles"
    :fleet-count="$fleetTotal"
    :pending-reservations-count="$pendingReservations"
>
    <x-slot name="headerActions">
        <a href="{{ route('vehicles.create') }}"
            class="gold-gradient rounded-md px-5 py-2 text-sm font-bold text-white shadow-lg transition-all hover:brightness-110 active:scale-95">
            Ajouter un vehicule
        </a>
    </x-slot>

    @if (session('success'))
        <div class="rounded-xl border border-green-400 bg-green-100 px-6 py-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-5">
        <div class="rounded-xl bg-surface-container-lowest p-6 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
            <p class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Total Vehicles</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-black text-primary-container">{{ $fleetTotal }}</h3>
                <span class="material-symbols-outlined text-slate-300">directions_car</span>
            </div>
        </div>
        <div class="rounded-xl bg-surface-container-lowest p-6 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
            <p class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Available</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-black text-primary-container">{{ $availableVehicles }}</h3>
                <span class="rounded bg-green-100 px-2 py-1 text-xs font-bold text-green-700">Ready</span>
            </div>
        </div>
        <div class="rounded-xl bg-surface-container-lowest p-6 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
            <p class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Unavailable</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-black text-primary-container">{{ $unavailableVehicles }}</h3>
                <span class="rounded bg-red-100 px-2 py-1 text-xs font-bold text-red-700">Busy</span>
            </div>
        </div>
        <div class="rounded-xl bg-surface-container-lowest p-6 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
            <p class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Featured / GPS</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-black text-primary-container">{{ $featuredVehicles }} <span class="text-sm font-medium text-on-surface-variant">/ {{ $gpsVehicles }}</span></h3>
                <span class="material-symbols-outlined text-slate-300">star</span>
            </div>
        </div>
        <div class="rounded-xl bg-surface-container-lowest p-6 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
            <p class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Average Price</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-black text-primary-container">{{ number_format($averagePricePerDay, 0, ',', ' ') }} <span class="text-sm font-medium">DH</span></h3>
                <span class="material-symbols-outlined text-slate-300">payments</span>
            </div>
        </div>
    </div>

    <section class="rounded-2xl bg-surface-container-lowest p-8 shadow-[0_8px_30px_rgba(13,33,55,0.02)]">
        <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <span class="text-sm font-bold uppercase tracking-[0.2em] text-secondary">Mon Agence</span>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-primary-container">Mes Vehicules</h1>
                <p class="mt-2 text-on-surface-variant">{{ $agency->name }}</p>
            </div>
            <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                <div class="rounded-xl bg-surface-container-low px-4 py-3 text-sm font-semibold text-on-surface-variant">
                    {{ $vehicles->total() }} vehicule(s) trouves
                </div>
                <a href="{{ route('vehicles.create') }}"
                    class="gold-gradient inline-flex items-center justify-center gap-2 rounded-xl px-5 py-3 text-sm font-black uppercase tracking-wider text-white transition-all hover:brightness-110">
                    <span class="material-symbols-outlined text-base">add</span>
                    Ajouter un vehicule
                </a>
            </div>
        </div>

        <form method="GET" action="{{ route('agency.vehicles.manage') }}"
            class="mb-8 rounded-xl bg-surface-container-low p-4 shadow-[0_20px_40px_rgba(13,33,55,0.04)]">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-6">
                <div class="xl:col-span-2">
                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-outline-variant">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Marque, modele ou immatriculation"
                        class="w-full rounded-lg border-none bg-surface-container-high px-4 py-3 text-sm font-semibold text-on-surface outline-none focus:ring-0">
                </div>
                <div>
                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-outline-variant">Statut</label>
                    <select name="status"
                        class="w-full rounded-lg border-none bg-surface-container-high px-4 py-3 text-sm font-semibold text-on-surface outline-none focus:ring-0">
                        <option value="">Tous</option>
                        <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Disponible</option>
                        <option value="unavailable" {{ request('status') === 'unavailable' ? 'selected' : '' }}>Indisponible</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-outline-variant">Transmission</label>
                    <select name="transmission"
                        class="w-full rounded-lg border-none bg-surface-container-high px-4 py-3 text-sm font-semibold text-on-surface outline-none focus:ring-0">
                        <option value="">Toutes</option>
                        <option value="manual" {{ request('transmission') === 'manual' ? 'selected' : '' }}>Manuelle</option>
                        <option value="automatic" {{ request('transmission') === 'automatic' ? 'selected' : '' }}>Automatique</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-outline-variant">Featured</label>
                    <select name="featured"
                        class="w-full rounded-lg border-none bg-surface-container-high px-4 py-3 text-sm font-semibold text-on-surface outline-none focus:ring-0">
                        <option value="">Tous</option>
                        <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Oui</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <label class="flex w-full items-center gap-3 rounded-lg bg-surface-container-high px-4 py-3 text-sm font-semibold text-on-surface">
                        <input type="checkbox" name="has_gps" value="1" {{ request('has_gps') ? 'checked' : '' }}
                            class="h-4 w-4 rounded border-outline-variant text-secondary focus:ring-secondary">
                        Avec GPS
                    </label>
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-on-surface-variant">Affinez votre flotte comme sur un vrai panneau de gestion.</p>
                <div class="flex gap-3">
                    <a href="{{ route('agency.vehicles.manage') }}"
                        class="rounded-lg px-4 py-3 text-sm font-bold text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-on-surface">
                        Reinitialiser
                    </a>
                    <button type="submit"
                        class="gold-gradient rounded-lg px-6 py-3 text-sm font-black uppercase tracking-wider text-white transition-opacity hover:opacity-90">
                        Filtrer
                    </button>
                </div>
            </div>
        </form>

        @if ($vehicles->isEmpty())
            <div class="py-24 text-center">
                <span class="material-symbols-outlined mb-4 block text-6xl text-on-surface-variant">directions_car</span>
                <p class="mb-6 text-xl font-semibold text-on-surface-variant">Aucun vehicule ne correspond aux filtres actuels.</p>
                <a href="{{ route('vehicles.create') }}"
                    class="gold-gradient inline-flex items-center gap-2 rounded-xl px-8 py-4 font-black uppercase tracking-widest text-white">
                    <span class="material-symbols-outlined">add</span>
                    Ajouter un vehicule
                </a>
            </div>
        @else
            <div class="overflow-hidden rounded-xl border border-surface-container">
                <div class="grid grid-cols-12 gap-4 bg-primary-container px-6 py-4 text-xs font-bold uppercase tracking-widest text-white">
                    <div class="col-span-1">Photo</div>
                    <div class="col-span-3">Vehicule</div>
                    <div class="col-span-1">Annee</div>
                    <div class="col-span-1">Places</div>
                    <div class="col-span-1">Transmission</div>
                    <div class="col-span-1">GPS</div>
                    <div class="col-span-1">Prix/jour</div>
                    <div class="col-span-1">Statut</div>
                    <div class="col-span-2 text-right">Actions</div>
                </div>

                @foreach ($vehicles as $vehicle)
                    <div class="grid grid-cols-12 items-center gap-4 border-b border-surface-container px-6 py-4 transition-colors hover:bg-surface-container-low">
                        <div class="col-span-1">
                            <img src="{{ $vehicle->primary_photo_url }}"
                                alt="{{ $vehicle->brand }}"
                                class="h-14 w-14 rounded-lg object-cover">
                        </div>

                        <div class="col-span-3">
                            <div class="flex items-center gap-2">
                                <p class="font-bold text-on-surface">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                                @if ($vehicle->is_featured)
                                    <span class="rounded bg-secondary-container px-2 py-0.5 text-[10px] font-bold uppercase tracking-widest text-on-secondary-container">
                                        Featured
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-on-surface-variant">{{ $vehicle->color }} · {{ $vehicle->fuel_type_label }}</p>
                        </div>

                        <div class="col-span-1 text-sm text-on-surface">{{ $vehicle->year }}</div>

                        <div class="col-span-1">
                            <span class="flex items-center gap-1 text-sm text-on-surface">
                                <span class="material-symbols-outlined text-base">airline_seat_recline_normal</span>
                                {{ $vehicle->seats }}
                            </span>
                        </div>

                        <div class="col-span-1 text-sm text-on-surface">{{ ucfirst($vehicle->transmission) }}</div>

                        <div class="col-span-1">
                            @if ($vehicle->has_gps)
                                <a href="{{ route('agency.vehicles.gps', $vehicle) }}" aria-label="Suivre le GPS de {{ $vehicle->brand }} {{ $vehicle->model }}" class="inline-flex items-center gap-1 text-xs font-bold text-secondary underline">
                                    <span class="material-symbols-outlined text-base" aria-hidden="true">location_on</span>Suivre GPS
                                </a>
                            @else
                                <span class="material-symbols-outlined text-on-surface-variant">remove</span>
                            @endif
                        </div>

                        <div class="col-span-1">
                            <span class="font-black text-secondary">{{ $vehicle->price_per_day }} DH</span>
                        </div>

                        <div class="col-span-1">
                            <span class="rounded px-2 py-1 text-[10px] font-bold uppercase tracking-widest {{ $vehicle->status == 'available' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $vehicle->status == 'available' ? 'Dispo' : 'Indispo' }}
                            </span>
                        </div>

                        <div class="col-span-2 flex justify-end gap-2">
                            <a href="{{ route('vehicles.show', $vehicle) }}"
                                class="flex items-center rounded bg-surface-container px-3 py-2 text-sm font-bold text-on-surface transition-colors hover:bg-surface-container-high">
                                <span class="material-symbols-outlined text-base">visibility</span>
                            </a>
                            <a href="{{ route('vehicles.edit', $vehicle) }}"
                                class="flex items-center rounded bg-primary-container px-3 py-2 text-sm font-bold text-white transition-colors hover:bg-primary">
                                <span class="material-symbols-outlined text-base">edit</span>
                            </a>
                            <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
                                onsubmit="return confirm('Supprimer ce vehicule ?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                    class="flex items-center rounded bg-red-100 px-3 py-2 text-sm font-bold text-red-700 transition-colors hover:bg-red-200">
                                    <span class="material-symbols-outlined text-base">delete</span>
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $vehicles->links() }}
            </div>
        @endif
    </section>
</x-agency-dashboard-layout>
