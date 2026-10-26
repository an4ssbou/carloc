@props(['vehicle', 'featured' => false])
<article class="catalog-card catalog-paper group rounded-xl overflow-hidden shadow-sm h-full {{ $featured ? 'lg:grid lg:grid-cols-2' : 'flex flex-col' }}" aria-label="{{ $vehicle->brand }} {{ $vehicle->model }}{{ $featured ? ' — véhicule à la une' : '' }}">
    <div class="catalog-photo relative overflow-hidden {{ $featured ? 'lg:aspect-auto lg:min-h-[280px]' : '' }}">
        <div class="absolute inset-0 flex flex-col items-center justify-center gap-3 text-white/60" aria-hidden="true">
            <svg viewBox="0 0 120 60" fill="none" class="w-32 h-16" focusable="false"><path d="m17 36 8-19a6 6 0 0 1 5-3h49a6 6 0 0 1 5 3l14 19m-81 0h81m-81 0-6 4v10h11m76-14 8 4v10H95M37 50h42M32 18l-5 18m48-18 9 18" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/><circle cx="29" cy="49" r="8" stroke="currentColor" stroke-width="3"/><circle cx="87" cy="49" r="8" stroke="currentColor" stroke-width="3"/></svg>
            <span class="text-xs font-medium tracking-wide">Photo non disponible</span>
        </div>
        @if($vehicle->primaryPhoto)
            <img src="{{ $vehicle->primary_photo_url }}" alt="{{ $vehicle->brand }} {{ $vehicle->model }}" loading="lazy" class="absolute inset-0 w-full h-full object-cover" onerror="this.hidden = true">
        @endif
        <div class="absolute top-4 left-4 right-4 flex flex-wrap justify-between gap-2">
            <span class="catalog-primary px-3 py-1 text-[10px] font-bold uppercase tracking-widest rounded">{{ $vehicle->agency?->name ?? 'Agence' }}</span>
            @if($featured)<span class="gold-gradient text-white px-3 py-1 text-[10px] font-bold uppercase tracking-widest rounded">À la une</span>@endif
        </div>
    </div>
    <div class="p-6 flex flex-col flex-1 min-w-0">
        <h3 class="text-xl font-extrabold mb-1">{{ $vehicle->brand }} {{ $vehicle->model }}</h3>
        <p class="catalog-gold flex items-center gap-1 text-xs font-semibold uppercase tracking-tight mb-4"><span class="material-symbols-outlined text-base" aria-hidden="true">location_on</span>{{ $vehicle->agency?->city }}</p>
        <ul class="flex flex-wrap gap-x-4 gap-y-2 text-xs font-medium catalog-muted mb-4" aria-label="Caractéristiques">
            <li>{{ $vehicle->seats }} places</li>
            <li>{{ $vehicle->transmission === 'automatic' ? 'Automatique' : 'Manuelle' }}</li>
            <li>{{ $vehicle->fuel_type_label }}</li>
            @if($vehicle->has_gps)<li>GPS inclus</li>@endif
        </ul>
        <p class="text-sm catalog-muted leading-relaxed mb-6">{{ $vehicle->year }} · {{ $vehicle->color }} · {{ number_format($vehicle->mileage, 0, ',', ' ') }} km</p>
        <div class="catalog-soft mt-auto p-3 rounded-lg flex flex-wrap justify-between items-center gap-3">
            <div><span class="catalog-gold font-extrabold text-xl">{{ number_format($vehicle->price_per_day, 2, ',', ' ') }} DH</span><span class="catalog-muted text-xs"> / jour</span></div>
            <a href="{{ route('vehicles.show', $vehicle) }}" class="catalog-primary px-5 py-2 rounded font-bold text-xs hover:opacity-90 transition-opacity" aria-label="Voir {{ $vehicle->brand }} {{ $vehicle->model }}">Voir le véhicule</a>
        </div>
    </div>
</article>
