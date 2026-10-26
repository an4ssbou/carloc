@props([
    'title' => config('app.name', 'CarLoc'),
    'pendingAgenciesCount' => 0,
])

@php
    $isDashboardRoute = request()->routeIs('admin.dashboard');
    $isAgenciesRoute = request()->routeIs('admin.agencies*');
    $isUsersRoute = request()->routeIs('admin.users*');
    $isVehiclesRoute = request()->routeIs('admin.vehicles*');
    $isReservationsRoute = request()->routeIs('admin.reservations*');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <style>
            .material-symbols-outlined {
                font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
            }
            .gold-gradient {
                background: linear-gradient(135deg, #815500 0%, #feb234 100%);
            }
            .glass-nav {
                background: rgba(13, 33, 55, 0.8);
                backdrop-filter: blur(12px);
            }
        </style>
    </head>
    <body class="bg-surface font-body text-on-surface antialiased">
        <x-demo-notice />
        <aside
            class="fixed left-0 top-0 z-50 flex h-screen w-64 flex-col bg-[#0D2137] py-6 font-['Inter'] antialiased tracking-tight shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
            <div class="mb-10 flex items-center gap-3 px-6">
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-secondary-container">
                    <span class="material-symbols-outlined text-primary-container"
                        style="font-variation-settings: 'FILL' 1;">directions_car</span>
                </div>
                <div>
                    <h1 class="text-2xl font-black tracking-tighter text-white">CarLoc</h1>
                    <p class="text-xs font-medium uppercase tracking-widest text-slate-400">Super Admin</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 px-3">
                <a class="flex items-center gap-3 px-4 py-3 transition-all duration-300 ease-out active:scale-95 {{ $isDashboardRoute ? 'border-l-4 border-[#feb234] bg-[#172c44] font-semibold text-white' : 'text-slate-400 hover:bg-[#172c44] hover:text-white' }}"
                    href="{{ route('admin.dashboard') }}">
                    <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">dashboard</span>
                    <span>Dashboard</span>
                </a>
                <a class="flex items-center justify-between px-4 py-3 transition-all duration-300 ease-out active:scale-95 {{ $isAgenciesRoute ? 'border-l-4 border-[#feb234] bg-[#172c44] font-semibold text-white' : 'text-slate-400 hover:bg-[#172c44] hover:text-white' }}"
                    href="{{ route('admin.agencies') }}">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined">business_center</span>
                        <span>Agencies</span>
                    </div>
                    <span class="rounded bg-[#feb234] px-1.5 py-0.5 text-[10px] font-bold text-[#0D2137]">{{ $pendingAgenciesCount }}</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 transition-all duration-300 ease-out active:scale-95 {{ $isUsersRoute ? 'border-l-4 border-[#feb234] bg-[#172c44] font-semibold text-white' : 'text-slate-400 hover:bg-[#172c44] hover:text-white' }}"
                    href="{{ route('admin.users') }}">
                    <span class="material-symbols-outlined">group</span>
                    <span>Users</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 transition-all duration-300 ease-out active:scale-95 {{ $isVehiclesRoute ? 'border-l-4 border-[#feb234] bg-[#172c44] font-semibold text-white' : 'text-slate-400 hover:bg-[#172c44] hover:text-white' }}"
                    href="{{ route('admin.vehicles') }}">
                    <span class="material-symbols-outlined">directions_car</span>
                    <span>Vehicles</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 transition-all duration-300 ease-out active:scale-95 {{ $isReservationsRoute ? 'border-l-4 border-[#feb234] bg-[#172c44] font-semibold text-white' : 'text-slate-400 hover:bg-[#172c44] hover:text-white' }}"
                    href="{{ route('admin.reservations') }}">
                    <span class="material-symbols-outlined">calendar_month</span>
                    <span>Reservations</span>
                </a>
                <a class="flex items-center gap-3 px-4 py-3 text-slate-400 transition-all duration-300 ease-out hover:bg-[#172c44] hover:text-white active:scale-95"
                    href="#">
                    <span class="material-symbols-outlined">receipt_long</span>
                    <span>Logs</span>
                </a>
            </nav>

            <div class="mt-auto space-y-1 px-3 pt-6">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full items-center gap-3 px-4 py-3 text-slate-400 transition-all duration-300 ease-out hover:bg-[#172c44] hover:text-white active:scale-95">
                        <span class="material-symbols-outlined">logout</span>
                        <span>Logout</span>
                    </button>
                </form>
            </div>
        </aside>

        {{ $slot }}
    </body>
</html>
