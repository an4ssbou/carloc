<x-reservation-layout title="Mes réservations">
<p class="eyebrow">Espace client</p><h1>Mes réservations</h1><p>Retrouvez vos locations, leur simulation de paiement et leur récapitulatif.</p>
@forelse($reservations as $reservation)
<article class="card"><p class="eyebrow">{{ $reservation->status_label }}</p><h2>{{ $reservation->contract_snapshot['vehicle_name'] ?? ($reservation->vehicle?->brand.' '.$reservation->vehicle?->model) }}</h2>
<p class="reference">{{ $reservation->reference }}</p><p>{{ $reservation->start_date->format('d/m/Y') }} au {{ $reservation->end_date->format('d/m/Y') }} · {{ number_format($reservation->total_price, 2, ',', ' ') }} DH</p>
<div class="actions"><a class="button" href="{{ route('reservation.show', $reservation) }}">Voir les détails</a><a href="{{ route('reservation.contract', $reservation) }}">Récapitulatif de location</a></div></article>
@empty<div class="card"><h2>Aucune réservation pour le moment</h2><p>Choisissez une voiture pour commencer.</p><a class="button" href="{{ route('vehicles.index') }}">Voir les véhicules</a></div>@endforelse
{{ $reservations->links() }}
</x-reservation-layout>
