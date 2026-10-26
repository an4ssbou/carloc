@include('components.public-demo-notice')
@if(app()->environment('local') && \Illuminate\Support\Facades\Storage::disk('local')->exists('showcase/active.json'))
    <style>
        .showcase-notice{position:fixed;bottom:0;left:0;right:0;z-index:9999;display:flex;justify-content:center;align-items:center;gap:12px;flex-wrap:wrap;padding:11px 18px;background:#0d2137;color:#fff;font:12px/1.4 system-ui,sans-serif;border-top:2px solid #feb234;box-shadow:0 -4px 15px #0d21371a}.showcase-notice strong{color:#feb234;letter-spacing:.08em}.showcase-notice a{color:#ffddb2;text-decoration:underline}body{padding-bottom:48px}@media(max-width:600px){.showcase-notice{gap:4px 10px;font-size:10px;padding:8px 12px}body{padding-bottom:65px}}
    </style>
    <aside class="showcase-notice" data-showcase-notice role="note" aria-label="Données de démonstration">
        <strong>DÉMONSTRATION</strong>
        <span>Véhicules, clients et chiffres fictifs · photos illustratives</span>
        <a href="{{ asset('demo/vehicles/credits.html') }}" target="_blank" rel="noopener">Sources des photos</a>
    </aside>
@endif
