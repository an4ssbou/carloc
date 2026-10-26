<x-app-layout :title="$vehicle->brand . ' ' . $vehicle->model">

    {{-- Header --}}
    <section class="bg-primary-container pt-16 pb-20 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <nav class="flex items-center space-x-2 text-on-primary-container text-sm mb-6">
                <a href="{{ route('vehicles.index') }}" class="hover:text-white transition-colors">Véhicules</a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="text-white/60">{{ $vehicle->brand }} {{ $vehicle->model }}</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight">
                {{ $vehicle->brand }} {{ $vehicle->model }}
            </h1>
            <p class="text-on-primary-container text-lg">
                {{ $vehicle->year }} · {{ $vehicle->fuel_type_label }} · {{ $vehicle->condition_label }}
            </p>
            <div class="w-24 h-1 bg-secondary-container mt-8"></div>
        </div>
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
    </section>

    {{-- Contenu --}}
    <section class="max-w-7xl mx-auto px-6 -mt-12 mb-24">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-8 items-start">

            {{-- Colonne gauche --}}
            <div class="space-y-6">

               
                                {{-- Photos --}}
                    <div class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-[0_20px_40px_rgba(13,33,55,0.06)]"
                        x-data="{ active: '{{ $vehicle->primary_photo_url }}' }">
                        
                        {{-- Photo principale affichée --}}
                        <div class="aspect-[16/9] overflow-hidden">
                            <img :src="active"
                                alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                                class="w-full h-full object-cover transition-all duration-300">
                        </div>
    
                {{-- Miniatures cliquables --}}
                        @if($vehicle->photos->count() > 1)
                          <div class="grid grid-cols-4 gap-2 p-4">
                              @foreach($vehicle->photos as $photo)
                             <div class="aspect-square overflow-hidden rounded-lg cursor-pointer"
                                  @click="active = '{{ asset('storage/' . $photo->path) }}'">
                                    <img src="{{ asset('storage/' . $photo->path) }}"
                         alt="Photo {{ $loop->iteration }}"
                         class="w-full h-full object-cover hover:scale-105 transition-transform"
                         :class="active === '{{ asset('storage/' . $photo->path) }}' ? 'ring-2 ring-secondary' : ''">
                                 </div>
                         @endforeach
                         </div>
                            @endif
                        </div>
                    
                  

                {{-- Description --}}
                @if($vehicle->description)
                <div class="bg-surface-container-lowest rounded-xl p-8 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
                    <h2 class="text-xl font-bold text-primary-container mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">description</span>
                        Description
                    </h2>
                    <p class="text-on-surface-variant leading-relaxed">{{ $vehicle->description }}</p>
                </div>
                @endif

                {{-- Caractéristiques --}}
                <div class="bg-surface-container-lowest rounded-xl p-8 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
                    <h2 class="text-xl font-bold text-primary-container mb-6 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">settings</span>
                        Caractéristiques
                    </h2>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-6">

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Marque</span>
                            <span class="font-semibold text-on-surface">{{ $vehicle->brand }}</span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Modèle</span>
                            <span class="font-semibold text-on-surface">{{ $vehicle->model }}</span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Année</span>
                            <span class="font-semibold text-on-surface">{{ $vehicle->year }}</span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Couleur</span>
                            <span class="font-semibold text-on-surface">{{ $vehicle->color }}</span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Carburant</span>
                            <span class="font-semibold text-on-surface">{{ $vehicle->fuel_type_label }}</span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Transmission</span>
                            <span class="font-semibold text-on-surface">{{ ucfirst($vehicle->transmission) }}</span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Places</span>
                            <span class="font-semibold text-on-surface">{{ $vehicle->seats }}</span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">Kilométrage</span>
                            <span class="font-semibold text-on-surface">{{ number_format($vehicle->mileage) }} km</span>
                        </div>

                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">État</span>
                            <span class="font-semibold text-on-surface">{{ $vehicle->condition_label }}</span>
                        </div>

                        @if($vehicle->has_gps)
                        <div class="flex flex-col gap-1">
                            <span class="text-xs font-bold text-on-surface-variant uppercase tracking-wider">GPS</span>
                            <span class="font-semibold text-secondary flex items-center gap-1">
                                <span class="material-symbols-outlined text-lg">check_circle</span>
                                Inclus
                            </span>
                        </div>
                        @endif

                    </div>
                </div>

            </div>

            {{-- Colonne droite sticky --}}
            <aside class="lg:sticky lg:top-28 space-y-6">

                {{-- Prix et réservation --}}
                <div class="bg-primary-container text-white rounded-xl p-8">
                    <div class="mb-6">
                        <span class="text-secondary-container text-4xl font-black">{{ $vehicle->price_per_day }} DH</span>
                        <span class="text-on-primary-container text-sm">/jour</span>
                    </div>

                    @if($vehicle->price_week || $vehicle->price_two_weeks || $vehicle->price_month)
                    <div class="space-y-3 mb-8 pt-6 border-t border-white/10">
                        <p class="text-xs font-bold uppercase tracking-widest text-on-primary-container mb-4">Tarifs spéciaux</p>
                        @if($vehicle->price_week)
                        <div class="flex justify-between items-center">
                            <span class="text-on-primary-container text-sm">Par semaine</span>
                            <span class="text-white font-bold">{{ $vehicle->price_week }} DH</span>
                        </div>
                        @endif
                        @if($vehicle->price_two_weeks)
                        <div class="flex justify-between items-center">
                            <span class="text-on-primary-container text-sm">2 semaines</span>
                            <span class="text-white font-bold">{{ $vehicle->price_two_weeks }} DH</span>
                        </div>
                        @endif
                        @if($vehicle->price_month)
                        <div class="flex justify-between items-center">
                            <span class="text-on-primary-container text-sm">Par mois</span>
                            <span class="text-white font-bold">{{ $vehicle->price_month }} DH</span>
                        </div>
                        @endif
                    </div>
                    @endif

                    <a href="{{ route('reservation.create', $vehicle) }}"
                        class="w-full gold-gradient text-primary-container py-4 rounded-xl font-black text-lg uppercase tracking-widest flex items-center justify-center gap-3 shadow-xl hover:brightness-110 active:scale-[0.98] transition-all">

                            Réserver maintenant
                            <span class="material-symbols-outlined">calendar_month</span>

                        </a>
                                        </div>

      
                <div class="bg-surface-container-lowest rounded-xl p-8 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
                    <h3 class="text-lg font-bold text-primary-container mb-4 flex items-center gap-2">
                        <span class="material-symbols-outlined text-secondary">store</span>
                        Agence
                    </h3>
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 bg-surface-container rounded-lg flex items-center justify-center">
                            <span class="material-symbols-outlined text-3xl text-on-surface-variant">business</span>
                        </div>
                        <div>
                            <p class="font-bold text-on-surface">{{ $vehicle->agency->name }}</p>
                            <p class="text-sm text-on-surface-variant">{{ $vehicle->agency->city }}</p>
                        </div>
                    </div>
                </div>

                {{-- Disponibilité --}}
                <div class="rounded-xl p-6 flex gap-4 border border-white/5
                    {{ $vehicle->is_available ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
                    <span class="material-symbols-outlined shrink-0 {{ $vehicle->is_available ? 'text-green-600' : 'text-red-600' }}"
                          style="font-variation-settings: 'FILL' 1;">
                        {{ $vehicle->is_available ? 'check_circle' : 'cancel' }}
                    </span>
                    <p class="text-sm font-bold {{ $vehicle->is_available ? 'text-green-700' : 'text-red-700' }}">
                        {{ $vehicle->is_available ? 'Disponible à la location' : 'Non disponible actuellement' }}
                    </p>
                </div>

            </aside>
        </div>
    </section>

</x-app-layout>
