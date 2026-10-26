const root = document.querySelector('[data-gps-tracking]');

if (root) {
    const config = JSON.parse(root.querySelector('[data-gps-config]').textContent);
    const el = name => root.querySelector(`[data-gps-${name}]`);
    const csrf = document.querySelector('meta[name="csrf-token"]').content;
    let data = config.initial;
    let clockOffset = Date.parse(data.server_time) - Date.now();
    let pollTimer, demoTimer, mapState;
    let loading = false, demoRunning = false, demoGeneration = 0, demoStep = 0, closed = false;
    let pollEpoch = 0, demoBusy = false, tokenBusy = false;
    const requests = new Set();
    let simulationRequest;
    const stamp = value => value ? new Intl.DateTimeFormat('fr-FR', {dateStyle:'short',timeStyle:'medium',timeZone:'UTC'}).format(new Date(value)) : '—';

    function showError(message) { el('error').textContent = message; el('error').hidden = false; }
    function clearError() { el('error').hidden = true; el('error').textContent = ''; }
    function stopDemo() {
        demoRunning = false;
        demoGeneration++;
        clearTimeout(demoTimer);
        simulationRequest?.abort();
        el('demo-state').textContent = 'Simulation arrêtée. Dernière position conservée.';
        el('demo-stop').disabled = true;
    }
    function render() {
        const enabled = Boolean(data.vehicle.has_gps);
        const point = data.position;
        const age = point ? Math.max(0, Math.floor((Date.now() + clockOffset - Date.parse(point.recorded_at)) / 1000)) : null;
        const status = !enabled ? 'disabled' : !point ? 'no_signal' : age > data.stale_after_seconds ? 'stale' : 'recent';
        el('status').dataset.status = status;
        el('status').textContent = {disabled:'GPS désactivé',no_signal:'Aucun signal reçu',recent:'Signal récent',stale:'Signal ancien'}[status];
        el('disabled').hidden = enabled;
        el('latitude').textContent = point ? Number(point.latitude).toFixed(7) : '—';
        el('longitude').textContent = point ? Number(point.longitude).toFixed(7) : '—';
        el('recorded').textContent = stamp(point?.recorded_at);
        el('received').textContent = stamp(point?.received_at);
        el('age').textContent = age === null ? '—' : age < 60 ? `${age} s` : `${Math.floor(age / 60)} min ${age % 60} s`;
        el('source').textContent = point ? (point.source === 'simulation' ? 'Source : simulation — positions fictives' : 'Source : émetteur connecté') : 'Source : aucune position';
        el('demo-warning').hidden = !demoRunning && point?.source !== 'simulation';
        if ((!enabled || !data.simulation_allowed) && demoRunning) stopDemo();
        el('demo-start').disabled = !enabled || !data.simulation_allowed || demoRunning;
        el('demo-stop').disabled = !demoRunning;
        if (!demoRunning) el('demo-state').textContent = !enabled ? 'Activez le GPS dans la fiche du véhicule.' : data.token_configured ? 'Une clé appareil est active. Révoquez-la avant de lancer une simulation.' : !data.simulation_allowed ? 'Simulation indisponible dans cet environnement.' : 'Simulation arrêtée. Positions de démonstration uniquement.';
        el('token-generate').disabled = !enabled || tokenBusy;
        el('token-revoke').disabled = !enabled || !data.token_configured || tokenBusy;
        el('token-state').textContent = data.token_configured ? 'Une clé de connexion est active.' : 'Aucune clé de connexion active.';
        el('center').disabled = !point || !mapState;
        if (!data.token_configured) { el('token-value').value = ''; el('token-reveal').hidden = true; }
        mapState?.update(point);
    }
    async function request(url, method = 'GET', payload, kind) {
        const controller = new AbortController();
        requests.add(controller);
        if (kind === 'simulation') simulationRequest = controller;
        const timeout = setTimeout(() => controller.abort(), 10000);
        try {
            const response = await fetch(url, {method, credentials:'same-origin', cache:'no-store', signal:controller.signal, headers:{Accept:'application/json','X-CSRF-TOKEN':csrf,...(payload ? {'Content-Type':'application/json'} : {})}, ...(payload ? {body:JSON.stringify(payload)} : {})});
            let body;
            try { body = await response.json(); } catch { throw new Error('Réponse du serveur indisponible. Réessayez.'); }
            if (!response.ok) {
                const message = [401,403,419].includes(response.status) ? 'Accès au suivi refusé ou session expirée. Reconnectez-vous avec le compte de cette agence.' : body.errors ? Object.values(body.errors).flat().join(' ') : body.message || body.error || 'La demande a échoué.';
                const error = new Error(message); error.status = response.status; throw error;
            }
            return body;
        } finally { clearTimeout(timeout); requests.delete(controller); if (simulationRequest === controller) simulationRequest = null; }
    }
    async function refresh() {
        if (loading || closed) return;
        loading = true;
        el('refresh').disabled = true;
        try {
            data = await request(config.positionUrl);
            clockOffset = Date.parse(data.server_time) - Date.now();
            clearError(); render();
        } catch (error) {
            if (!closed) showError(error.name === 'AbortError' ? 'Connexion interrompue. La dernière position connue est conservée.' : error instanceof TypeError ? 'Connexion impossible. La dernière position connue est conservée.' : error.message || 'Connexion impossible. La dernière position connue est conservée.');
            render();
        } finally { loading = false; el('refresh').disabled = false; }
    }
    async function poll(epoch = pollEpoch) {
        if (closed || document.hidden || epoch !== pollEpoch) return;
        clearTimeout(pollTimer);
        await refresh();
        if (!closed && !document.hidden && epoch === pollEpoch) pollTimer = setTimeout(() => poll(epoch), data.poll_interval_seconds * 1000);
    }
    async function demoTick(generation) {
        if (!demoRunning || generation !== demoGeneration || closed || demoBusy) return;
        demoBusy = true;
        try {
            const step = demoStep++ % 12;
            await request(config.simulateUrl, 'POST', {latitude:33.5731 + step * 0.00035,longitude:-7.5898 + step * 0.00022,recorded_at:new Date().toISOString()}, 'simulation');
            await refresh();
        } catch (error) {
            if (generation === demoGeneration && !closed) { stopDemo(); showError(error.name === 'AbortError' ? 'Envoi de simulation interrompu.' : error.message); render(); }
        } finally { demoBusy = false; }
        if (demoRunning && generation === demoGeneration && !document.hidden) demoTimer = setTimeout(() => demoTick(generation), 5000);
    }
    el('demo-start').addEventListener('click', () => {
        if (!data.simulation_allowed || demoRunning) return;
        demoRunning = true; const generation = ++demoGeneration;
        el('demo-state').textContent = 'Simulation en cours — un point fictif toutes les 5 secondes.';
        render(); demoTick(generation);
    });
    el('demo-stop').addEventListener('click', () => { stopDemo(); render(); });
    el('refresh').addEventListener('click', refresh);
    el('token-generate').addEventListener('click', async () => {
        stopDemo(); tokenBusy = true; render();
        try {
            const body = await request(config.tokenUrl, 'POST');
            data.token_configured = true; data.simulation_allowed = false;
            await refresh();
            el('token-value').value = body.token;
            el('token-reveal').hidden = false;
            el('show-token').checked = false; el('token-value').type = 'password';
            el('token-value').focus();
        } catch (error) { showError(error.message); } finally { tokenBusy = false; render(); }
    });
    el('token-revoke').addEventListener('click', async () => {
        tokenBusy = true; render();
        try { await request(config.tokenUrl, 'DELETE'); data.token_configured = false; el('token-value').value = ''; el('token-reveal').hidden = true; await refresh(); }
        catch (error) { showError(error.message); } finally { tokenBusy = false; render(); }
    });
    el('show-token').addEventListener('change', event => { el('token-value').type = event.target.checked ? 'text' : 'password'; });
    el('center').addEventListener('click', () => mapState?.center(data.position));

    async function initMap() {
        try {
            const module = await import('leaflet');
            await import('leaflet/dist/leaflet.css');
            const L = module.default;
            const map = L.map(el('map')).setView([31.8,-7.1], 5);
            let marker, tiles, lastPoint, tileFailed = false;
            mapState = {
                update(point) {
                    if (!point) { if (marker) { marker.remove(); marker = null; } lastPoint = null; return; }
                    const lat = Number(point.latitude), lng = Number(point.longitude);
                    if (!Number.isFinite(lat) || !Number.isFinite(lng)) return;
                    if (!marker) marker = L.circleMarker([lat,lng], {radius:10,weight:3,color:'#fff',fillOpacity:1}).addTo(map);
                    marker.setLatLng([lat,lng]).setStyle({fillColor:point.source === 'simulation' ? '#c88a22' : '#247548'});
                    const signature = `${lat},${lng}`;
                    if (lastPoint !== signature) map.setView([lat,lng], Math.max(map.getZoom(), 15), {animate:false});
                    lastPoint = signature;
                },
                center(point) { if (point) map.setView([point.latitude,point.longitude],15,{animate:false}); },
            };
            el('map-message').textContent = 'Fond de carte non chargé. Le repère et les coordonnées restent disponibles.';
            el('tiles').disabled = false;
            el('tiles').addEventListener('click', () => {
                if (!tiles) {
                    tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {maxZoom:19,attribution:'&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank" rel="noopener">OpenStreetMap</a> contributors'}).addTo(map);
                    tiles.on('tileerror', () => { tileFailed = true; el('map-message').textContent = 'Fond de carte indisponible. Les coordonnées et le suivi restent utilisables.'; });
                    tiles.on('load', () => { if (!tileFailed) el('map-message').textContent = 'Fond de carte OpenStreetMap chargé.'; });
                }
                el('tiles').disabled = true;
            });
            new ResizeObserver(() => map.invalidateSize({pan:false})).observe(el('map'));
            render();
        } catch { el('map-message').textContent = 'Carte indisponible. Les coordonnées et les commandes restent utilisables.'; }
    }
    document.addEventListener('visibilitychange', () => {
        pollEpoch++;
        clearTimeout(pollTimer); clearTimeout(demoTimer);
        if (!document.hidden && !closed) { poll(); if (demoRunning) demoTick(demoGeneration); }
    });
    window.addEventListener('pagehide', () => { closed = true; stopDemo(); clearTimeout(pollTimer); requests.forEach(controller => controller.abort()); el('token-value').value = ''; });
    const ageTimer = setInterval(() => { if (!closed && !document.hidden) render(); },1000);
    window.addEventListener('pagehide', () => clearInterval(ageTimer), {once:true});
    render(); initMap(); poll();
}
