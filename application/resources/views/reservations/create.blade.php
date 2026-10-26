<x-reservation-layout title="Réserver une voiture">
<p class="eyebrow">Votre prochaine location</p>
<h1>Réserver {{ $vehicle->brand }} {{ $vehicle->model }}</h1>
<p class="muted">{{ $vehicle->agency->name }} · {{ $vehicle->agency->city }}</p>
<div class="notice"><strong>Simulation locale</strong> : aucun paiement réel et aucune carte bancaire demandée. Après réservation, vous pourrez simuler un paiement et retrouver votre récapitulatif.</div>
<div class="grid">
<section class="card" aria-labelledby="dates-title">
<h2 id="dates-title">Choisir les dates</h2>
@if(!$profileRequirements['is_complete'])
<div class="notice error" role="alert">{{ $profileRequirements['message'] }} <a href="{{ route('profile.edit') }}">Compléter mon profil</a></div>
@endif
<p class="muted">Les documents déposés ne sont pas vérifiés par une IA. Le compte de démonstration utilise des données synthétiques.</p>
<form id="reservation-form" action="{{ route('reservation.store') }}" method="post">
@csrf<input type="hidden" name="vehicle_id" value="{{ $vehicle->id }}">
<label for="start_date">Date de départ</label>
<input type="date" id="start_date" name="start_date" required min="{{ now()->toDateString() }}" value="{{ old('start_date') }}">
<label for="end_date">Date de retour</label>
<input type="date" id="end_date" name="end_date" required min="{{ now()->addDay()->toDateString() }}" value="{{ old('end_date') }}">
<p id="date-help" class="muted">De 1 à 30 jours. Les dates de retour d'une réservation existante restent bloquées.</p>
<div id="reservation-error" class="notice error" role="alert" hidden></div>
<div class="actions"><button id="reserve-button" type="submit" @disabled(!$profileRequirements['is_complete'])>Enregistrer la réservation</button></div>
<p class="muted">En cas d'interruption, consultez <a href="{{ route('reservation.index') }}">mes réservations</a> avant de réessayer.</p>
</form>
</section>
<section class="card" aria-labelledby="pricing-title"><h2 id="pricing-title">Tarifs de cette voiture</h2>
<dl><dt>Prix par jour, moins de 7 jours</dt><dd>{{ number_format($vehicle->price_per_day, 2, ',', ' ') }} DH</dd>
@if($vehicle->price_week)<dt>Forfait de 7 à 14 jours</dt><dd>{{ number_format($vehicle->price_week, 2, ',', ' ') }} DH au total</dd>@endif
@if($vehicle->price_two_weeks)<dt>Forfait de 15 à 29 jours</dt><dd>{{ number_format($vehicle->price_two_weeks, 2, ',', ' ') }} DH au total</dd>@endif
@if($vehicle->price_month)<dt>Forfait 30 jours</dt><dd>{{ number_format($vehicle->price_month, 2, ',', ' ') }} DH au total</dd>@endif</dl>
<p>Le montant final est calculé par le serveur et affiché sur votre réservation.</p>
<h2>Annulation</h2><p>Possible pendant les 24 heures après création, jusqu'à l'échéance indiquée sur votre réservation.</p>
@if(count($vehicle->getBlockedDates()))<h2>Périodes déjà réservées</h2><ul>@foreach($vehicle->getBlockedDates() as $range)<li>{{ $range['from'] }} au {{ $range['to'] }}</li>@endforeach</ul>@endif
</section></div>
<script>
const form = document.getElementById('reservation-form');
form.addEventListener('submit', async event => {
 event.preventDefault();
 const button = document.getElementById('reserve-button');
 const error = document.getElementById('reservation-error');
 button.disabled = true; button.textContent = 'Enregistrement…'; error.hidden = true;
 try {
  const response = await fetch(form.action, {method:'POST', headers:{'Accept':'application/json','X-CSRF-TOKEN':form.querySelector('[name="_token"]').value}, body:new FormData(form)});
  const data = await response.json();
  if(!response.ok || !data.success) throw new Error(data.errors ? Object.values(data.errors).flat().join(' ') : (data.message || data.error || 'La réservation a échoué.'));
  window.location.assign(data.redirect_url);
 } catch(e) {
  error.textContent = e.message || 'Connexion interrompue. Consultez vos réservations avant de réessayer.'; error.hidden = false;
  button.disabled = false; button.textContent = 'Enregistrer la réservation';
 }
});
</script>
</x-reservation-layout>
