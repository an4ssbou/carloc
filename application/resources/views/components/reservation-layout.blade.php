@props(['title' => 'Mes locations'])
<!doctype html>
<html lang="fr">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}"><title>{{ $title }} — CarLoc</title>
<style>
:root{color-scheme:light;--navy:#0d2137;--gold:#815500;--line:#dbe2ea}*{box-sizing:border-box}body{margin:0;font:16px/1.6 system-ui,sans-serif;color:var(--navy);background:#f4f7fa}header{background:var(--navy);color:white}nav{max-width:1000px;margin:auto;padding:18px 22px;display:flex;gap:20px;align-items:center;flex-wrap:wrap}nav a{color:white}nav strong{font-size:1.4rem;margin-right:auto}main{max-width:920px;margin:40px auto;padding:0 20px 48px}h1{line-height:1.15;font-size:clamp(1.8rem,5vw,2.6rem)}h2{font-size:1.2rem}a{color:var(--gold);text-underline-offset:3px}a,button,input{touch-action:manipulation}:focus-visible{outline:3px solid #b47700;outline-offset:4px}.card{padding:26px;border:1px solid var(--line);border-radius:18px;background:white;margin:20px 0}.notice{padding:16px 20px;border-left:4px solid #b47700;background:#fff6e4;border-radius:8px}.error{background:#fff0f0;border-color:#b42318;color:#8a1912}.success{background:#edf8f1;border-color:#237647}.muted{color:#526479}.eyebrow{font-size:.8rem;letter-spacing:.12em;text-transform:uppercase;color:var(--gold);font-weight:700}label{display:block;font-weight:650;margin:16px 0 5px}input{width:100%;max-width:100%;font:inherit;border:1px solid #7d8b9b;border-radius:8px;padding:12px;background:white}button,.button{display:inline-block;max-width:100%;font:inherit;font-weight:650;line-height:1.5;background:var(--navy);color:white;border:1px solid transparent;border-radius:9px;padding:12px 20px;text-decoration:none;cursor:pointer}button:disabled{opacity:.6;cursor:wait}.secondary{background:white;color:var(--navy);border-color:var(--line)}.danger{background:#fff0f0;color:#8a1912;border-color:#d9a1a1}.actions{display:flex;flex-wrap:wrap;align-items:center;gap:12px;margin-top:24px}.actions form{margin:0}.grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px}dl{margin:0}dt{font-size:.85rem;color:#526479}dd{font-weight:650;margin:3px 0 18px;overflow-wrap:anywhere}.reference{overflow-wrap:anywhere;font-family:monospace}table{width:100%;border-collapse:collapse}th,td{text-align:left;border-bottom:1px solid var(--line);padding:10px 4px}footer{padding:20px;text-align:center;font-size:.85rem;color:#526479}.print-only{display:none}@media(max-width:580px){.grid{grid-template-columns:1fr}.card{padding:18px}nav{gap:12px;font-size:.9rem}main{margin-top:28px}}@media print{header,footer,.no-print{display:none!important}body{background:white}main{max-width:none;margin:0;padding:0}.card{break-inside:avoid}.print-only{display:block}}
</style>
</head>
<body>
@include('components.public-demo-notice')
<header><nav aria-label="Navigation principale"><strong>CarLoc<span style="color:#f3bd59">.</span></strong><a href="{{ route('vehicles.index') }}">Véhicules</a><a href="{{ route('reservation.index') }}">Mes réservations</a><a href="{{ route('profile.edit') }}">Mon profil</a></nav></header>
<main id="main-content">
@if(session('success'))<div class="notice success" role="status">{{ session('success') }}</div>@endif
@if(session('error'))<div class="notice error" role="alert">{{ session('error') }}</div>@endif
@if($errors->any())<div class="notice error" role="alert"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
{{ $slot }}
</main>
<footer>Version locale de démonstration · aucun paiement bancaire ni vérification d'identité.</footer>
</body></html>
