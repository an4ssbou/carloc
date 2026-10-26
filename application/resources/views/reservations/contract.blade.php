<x-reservation-layout title="Récapitulatif de location">
<p class="eyebrow">Document de démonstration</p><h1>Récapitulatif de location</h1>
<p class="reference">{{ $reservation->reference }}</p>
<div class="notice">Ce récapitulatif n'est pas un contrat signé ni une preuve de paiement bancaire. Aucune vérification d'identité ou de permis n'a été effectuée.</div>
<section class="card"><h2>État actuel</h2><p><strong>{{ $reservation->status_label }}</strong></p>
<p>@if($reservation->payment_status === 'simulated')Paiement simulé — aucun débit.@elseif($reservation->payment_status === 'simulation_cancelled')Simulation annulée — aucun débit ni remboursement bancaire.@else Paiement non simulé — aucun encaissement réel.@endif</p>
@if($reservation->payment_reference)<p class="reference">{{ $reservation->payment_reference }}</p>@endif
</section>
@if($snapshot)
<section class="card"><h2>Informations enregistrées à la réservation</h2><div class="grid"><dl>
<dt>Client</dt><dd>{{ $snapshot['client_name'] }}</dd><dt>Email</dt><dd>{{ $snapshot['client_email'] }}</dd>
<dt>Agence</dt><dd>{{ $snapshot['agency_name'] }}</dd><dt>Ville et adresse de l'agence</dt><dd>{{ $snapshot['agency_city'] }} · {{ $snapshot['agency_address'] }}</dd>
</dl><dl><dt>Véhicule</dt><dd>{{ $snapshot['vehicle_name'] }}</dd><dt>Immatriculation</dt><dd>{{ $snapshot['registration_number'] }}</dd>
<dt>Dates</dt><dd>{{ $snapshot['start_date'] }} au {{ $snapshot['end_date'] }}</dd><dt>Durée</dt><dd>{{ $snapshot['duration_days'] }} jour(s)</dd>
<dt>Montant total enregistré</dt><dd>{{ number_format((float)$snapshot['total_price'], 2, ',', ' ') }} DH</dd></dl></div>
<p class="muted">Instantané version {{ $snapshot['version'] }}. Les informations des parties et les tarifs ci-dessus restent ceux enregistrés à la création. Le statut actuel est affiché séparément.</p>
</section>
@else<div class="notice">Cette ancienne réservation ne possède pas d'instantané de récapitulatif. Aucun contrat historique n'a été reconstitué.</div>@endif
<p>Règle locale : annulation possible jusqu'à 24 heures après création. Échéance : {{ $reservation->getClientCancellationDeadline()->format('d/m/Y H:i:s') }} ({{ config('app.timezone') }}).</p>
<div class="actions no-print"><button type="button" onclick="window.print()">Imprimer / enregistrer en PDF</button><a href="{{ route('reservation.show', $reservation) }}">Retour à la réservation</a></div>
</x-reservation-layout>
