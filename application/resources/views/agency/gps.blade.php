<x-app-layout :title="'Suivi GPS — ' . $vehicle->brand . ' ' . $vehicle->model">
    <style>
        .gps-shell{max-width:1240px;margin:auto;padding:110px 24px 60px;color:#0d2137;font-family:Inter,system-ui,sans-serif}.gps-shell *{box-sizing:border-box}.gps-shell [hidden]{display:none!important}.gps-top{display:flex;justify-content:space-between;align-items:center;gap:20px;flex-wrap:wrap;margin-bottom:26px}.gps-shell h1{font-size:clamp(28px,4vw,40px);font-weight:800;line-height:1.2;margin:12px 0}.gps-shell h2{font-size:18px;font-weight:750;margin:0 0 16px}.gps-eyebrow{font-size:11px;letter-spacing:.16em;text-transform:uppercase;font-weight:800;color:#815500}.gps-muted{color:#526479;font-size:14px}.gps-card{padding:24px;background:white;border:1px solid #e2e7ed;border-radius:16px;min-width:0}.gps-grid{display:grid;grid-template-columns:minmax(0,1fr) 340px;gap:24px}.gps-map{height:440px;background:repeating-linear-gradient(0deg,#e8edf2 0,#e8edf2 1px,transparent 1px,transparent 30px),repeating-linear-gradient(90deg,#e8edf2 0,#e8edf2 1px,#f6f8fb 1px,#f6f8fb 30px);border:1px solid #e2e7ed;border-radius:12px;z-index:0}.gps-controls{display:flex;flex-wrap:wrap;align-items:center;gap:12px;margin-top:18px}.gps-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;font:inherit;font-size:13px;font-weight:750;line-height:1.5;padding:11px 17px;border:1px solid #d9e0e9;border-radius:9px;color:#0d2137;background:white;cursor:pointer;text-decoration:none}.gps-btn-primary{background:#0d2137;color:white;border-color:#0d2137}.gps-btn-gold{background:linear-gradient(135deg,#815500,#feb234);border-color:transparent;color:white}.gps-btn:disabled{opacity:.5;cursor:not-allowed}.gps-shell :is(button,a,input,summary):focus-visible{outline:3px solid #c88a22;outline-offset:4px}.gps-notice{padding:15px 18px;margin-bottom:20px;border:1px solid #e8ce91;border-left:4px solid #b67a12;background:#fff8e8;border-radius:10px;font-size:14px}.gps-notice-error{border-color:#dba3a3;background:#fff0f0;color:#8a1f1f}.gps-badge{display:inline-flex;align-items:center;gap:8px;padding:7px 12px;border-radius:30px;background:#eef2f7;color:#526479;font-size:12px;font-weight:800}.gps-badge[data-status=recent]{background:#e5f4e9;color:#23633c}.gps-badge[data-status=stale]{background:#fff0d3;color:#815500}.gps-data{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-top:20px}.gps-data dt{font-size:11px;color:#526479;text-transform:uppercase;letter-spacing:.05em}.gps-data dd{font-size:15px;font-weight:700;margin:4px 0 0;overflow-wrap:anywhere}.gps-data .gps-wide{grid-column:1/-1}.gps-side{display:flex;flex-direction:column;gap:20px}.gps-endpoint{overflow-wrap:anywhere;font-family:monospace;font-size:12px;padding:10px;background:#f0f4f8;border-radius:7px;margin:10px 0}.gps-secret{width:100%;min-width:0;padding:10px;border:1px solid #8f9baa;border-radius:7px;font:13px monospace}.gps-shell summary{cursor:pointer;font-weight:750;font-size:15px}.gps-source{display:block;font-size:13px;font-weight:700;margin-top:12px}.gps-shell .leaflet-control-attribution{font-size:10px}.gps-shell details p{margin-top:12px}@media(max-width:850px){.gps-grid{grid-template-columns:1fr}.gps-map{height:330px}.gps-shell{padding:95px 18px 40px}.gps-card{padding:18px}.gps-top{align-items:flex-start}.gps-controls .gps-btn{flex-grow:1}}@media(prefers-reduced-motion:reduce){.gps-shell *{scroll-behavior:auto!important}}
    </style>
    <div class="gps-shell" data-gps-tracking>
        <div class="gps-top">
            <div>
                <a class="gps-muted" href="{{ route('agency.vehicles.manage') }}">← Mes véhicules</a>
                <h1>Suivi GPS</h1>
                <p class="gps-muted">{{ $vehicle->brand }} {{ $vehicle->model }} · {{ $vehicle->registration_number }} · {{ $agency->name }}</p>
            </div>
            <a class="gps-btn" href="{{ route('vehicles.edit', $vehicle) }}">Modifier le véhicule</a>
        </div>

        <div class="gps-notice" data-gps-disabled @if($vehicle->has_gps) hidden @endif>
            <strong>GPS désactivé.</strong> Activez l’option GPS dans la fiche du véhicule pour recevoir des positions. La dernière position et l’ancienne clé sont effacées lors de la désactivation.
        </div>
        <div class="gps-notice" data-gps-demo-warning hidden role="status"><strong>Simulation — positions fictives.</strong> Aucun déplacement réel n’est mesuré. La dernière position simulée reste identifiée après l’arrêt.</div>
        <div class="gps-notice gps-notice-error" data-gps-error hidden role="alert"></div>
        <div class="gps-grid">
            <section class="gps-card" aria-labelledby="gps-map-heading">
                <div class="gps-top" style="margin-bottom:16px">
                    <div><span class="gps-eyebrow">Localisation privée</span><h2 id="gps-map-heading" style="margin:5px 0 0">Dernière position</h2></div>
                    <span class="gps-badge" data-gps-status data-status="{{ $initialTracking['status'] }}" role="status">{{ ['disabled' => 'GPS désactivé', 'no_signal' => 'Aucun signal reçu', 'recent' => 'Signal récent', 'stale' => 'Signal ancien'][$initialTracking['status']] }}</span>
                </div>
                <div class="gps-map" data-gps-map role="region" aria-label="Carte de la dernière position du véhicule"></div>
                <p class="gps-source" data-gps-source>{{ !$initialTracking['position'] ? 'Source : aucune position' : ($initialTracking['position']['source'] === 'simulation' ? 'Source : simulation — positions fictives' : 'Source : émetteur connecté') }}</p>
                <div class="gps-controls">
                    <button type="button" class="gps-btn gps-btn-primary" data-gps-refresh>Actualiser</button>
                    <button type="button" class="gps-btn" data-gps-center>Recentrer</button>
                    <button type="button" class="gps-btn" data-gps-tiles disabled>Charger le fond de carte</button>
                </div>
                <p class="gps-muted" style="margin-top:12px">Le fond OpenStreetMap est facultatif et chargé uniquement à votre demande. Les coordonnées restent consultables sans lui.</p>
                <p class="gps-muted" data-gps-map-message role="status">Chargement de la carte…</p>
                <dl class="gps-data">
                    <div><dt>Latitude</dt><dd data-gps-latitude>{{ $initialTracking['position']['latitude'] ?? '—' }}</dd></div>
                    <div><dt>Longitude</dt><dd data-gps-longitude>{{ $initialTracking['position']['longitude'] ?? '—' }}</dd></div>
                    <div><dt>Mesurée le (UTC)</dt><dd data-gps-recorded>{{ $initialTracking['position']['recorded_at'] ?? '—' }}</dd></div>
                    <div><dt>Reçue le (UTC)</dt><dd data-gps-received>{{ $initialTracking['position']['received_at'] ?? '—' }}</dd></div>
                    <div class="gps-wide"><dt>Âge du signal</dt><dd data-gps-age>—</dd></div>
                </dl>
                <p class="gps-muted" style="margin-top:20px">Actualisation toutes les 5 secondes. Un signal devient ancien après 60 secondes sans nouvelle mesure. Le suivi est suspendu quand cet onglet est masqué.</p>
            </section>
            <div class="gps-side">
                <section class="gps-card" aria-labelledby="gps-demo-heading">
                    <span class="gps-eyebrow">Démonstration locale</span>
                    <h2 id="gps-demo-heading" style="margin-top:6px">Essayer le suivi</h2>
                    <p class="gps-muted">Sans boîtier connecté, envoyez des positions fictives pour voir la carte s’actualiser. Aucun GPS réel n’est utilisé.</p>
                    <div class="gps-controls">
                        <button type="button" class="gps-btn gps-btn-gold" data-gps-demo-start disabled>Démarrer la simulation</button>
                        <button type="button" class="gps-btn" data-gps-demo-stop disabled>Arrêter</button>
                    </div>
                    <p class="gps-muted" data-gps-demo-state style="margin-top:12px">Simulation arrêtée.</p>
                </section>
                <section class="gps-card">
                    <details>
                        <summary>Raccorder un boîtier GPS</summary>
                        <p class="gps-muted">Un boîtier devra envoyer ses positions à cette adresse. Sa clé est propre à ce véhicule et ne donne aucun accès au compte agence.</p>
                        <p class="gps-endpoint">POST {{ url('/api/gps/vehicles/' . $vehicle->uuid . '/positions') }}</p>
                        <p class="gps-muted" data-gps-token-state>{{ $initialTracking['token_configured'] ? 'Une clé de connexion est active.' : 'Aucune clé de connexion active.' }}</p>
                        <div class="gps-controls">
                            <button type="button" class="gps-btn gps-btn-primary" data-gps-token-generate>Créer / renouveler la clé</button>
                            <button type="button" class="gps-btn" data-gps-token-revoke>Révoquer la clé</button>
                        </div>
                        <p class="gps-muted">Créer une clé invalide immédiatement l’ancienne et arrête la démonstration. La clé complète n’est affichée qu’une fois.</p>
                        <div data-gps-token-reveal hidden style="margin-top:14px">
                            <label for="gps-generated-key" class="gps-muted">Nouvelle clé (à conserver dans le boîtier)</label>
                            <input id="gps-generated-key" class="gps-secret" type="password" readonly autocomplete="off" data-gps-token-value>
                            <label class="gps-muted" style="display:block;margin-top:8px"><input type="checkbox" data-gps-show-token> Afficher la clé</label>
                        </div>
                    </details>
                </section>
            </div>
        </div>
        <noscript><p class="gps-notice">JavaScript est nécessaire pour l’actualisation et les commandes GPS. Les dernières informations enregistrées sont affichées ci-dessus.</p></noscript>
        @php
            $gpsConfig = [
                'initial' => $initialTracking,
                'positionUrl' => route('agency.vehicles.gps.position', $vehicle),
                'simulateUrl' => route('agency.vehicles.gps.simulate', $vehicle),
                'tokenUrl' => route('agency.vehicles.gps.token', $vehicle),
            ];
        @endphp
        <script type="application/json" data-gps-config>@json($gpsConfig)</script>
    </div>
</x-app-layout>
