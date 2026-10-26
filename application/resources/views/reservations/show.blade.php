<x-reservation-layout title="Ma réservation">
<p class="eyebrow">Ma location · {{ $reservation->status_label }}</p>
<h1>{{ $reservation->contract_snapshot['vehicle_name'] ?? ($reservation->vehicle?->brand.' '.$reservation->vehicle?->model) }}</h1>
<p class="reference">{{ $reservation->reference }}</p>
<div class="grid"><section class="card"><h2>Votre réservation</h2>
<dl><dt>Agence</dt><dd>{{ $reservation->contract_snapshot['agency_name'] ?? $reservation->agency?->name }}</dd>
<dt>Période</dt><dd>{{ $reservation->start_date->format('d/m/Y') }} au {{ $reservation->end_date->format('d/m/Y') }}</dd>
<dt>Durée</dt><dd>{{ $reservation->duration_days }} jour(s)</dd>
<dt>Montant total</dt><dd>{{ number_format($reservation->total_price, 2, ',', ' ') }} DH</dd>
<dt>Statut de réservation</dt><dd>{{ $reservation->status_label }}</dd></dl>
<a class="button secondary" href="{{ route('reservation.contract', $reservation) }}">Consulter le récapitulatif de location</a>
</section><section class="card"><h2>Paiement de démonstration</h2>
@if($reservation->payment_status === 'simulated')
<div class="notice success" role="status"><strong>Paiement simulé — aucun débit</strong></div><p class="reference">{{ $reservation->payment_reference }}</p>
@elseif($reservation->payment_status === 'simulation_cancelled')
<div class="notice">Simulation annulée. Aucun débit ni remboursement bancaire.</div>
@else
<p>Aucun paiement réel effectué. La simulation conserve uniquement une référence locale.</p>
@if($reservation->isActive() && config('payments.demo_enabled'))
<form method="post" action="{{ route('reservation.simulate-payment', $reservation) }}">@csrf<button type="submit">Simuler le paiement</button></form>
@endif
@endif
<p class="muted">Aucune carte bancaire demandée. Cette version n'encaisse pas d'argent.</p>
</section></div>
<section class="card"><h2>Annulation</h2>
@if($reservation->canBeCancelledByClient())
<p>Annulation possible pendant les 24 heures après création, jusqu'au <strong>{{ $reservation->getClientCancellationDeadline()->format('d/m/Y H:i:s') }}</strong> (heure du serveur : {{ config('app.timezone') }}).</p>
<form method="post" action="{{ route('reservation.cancel', $reservation) }}">@csrf<button type="submit" class="danger">Annuler la réservation</button></form>
@elseif($reservation->isActive())<p>Le délai de 24 heures après création est dépassé.</p>
@else<p>Cette réservation n'est plus annulable. Le récapitulatif reste consultable.</p>@endif
</section>
<div class="actions"><a href="{{ route('reservation.index') }}">Toutes mes réservations</a><a href="{{ route('vehicles.index') }}">Voir les véhicules</a></div>
</x-reservation-layout>
