@props([
    'agency',
    'pageTitle' => 'Dashboard',
    'pageHeading' => 'Overview',
    'pageBreadcrumb' => 'Dashboard',
    'fleetCount' => 0,
    'pendingReservationsCount' => 0,
])

@php
    $isDashboardRoute = request()->routeIs('agency.dashboard');
    $isVehiclesRoute = request()->routeIs('agency.vehicles.manage') || request()->routeIs('vehicles.create') || request()->routeIs('vehicles.edit');
    $isReservationsRoute = request()->routeIs('agency.reservations.*');
    $isNotificationsRoute = request()->routeIs('notifications.*');
    $unreadNotificationsCount = auth()->user()?->unreadNotifications()->count() ?? 0;
@endphp

<x-app-layout :title="$pageTitle">
    <aside class="fixed left-0 top-0 z-50 flex h-screen w-64 flex-col bg-[#0D2137] p-6 shadow-[20px_0_40px_rgba(13,33,55,0.06)]">
        <div class="mb-10 flex items-center space-x-3">
            <div class="relative">
                <img class="h-12 w-12 rounded-full border-2 border-secondary object-cover"
                    src="{{ $agency->logo_url }}"
                    alt="{{ $agency->name }}" />
                <span class="absolute bottom-0 right-0 block h-3 w-3 rounded-full border-2 border-[#0D2137] bg-tertiary-fixed-dim"></span>
            </div>
            <div>
                <h1 class="text-lg font-bold leading-tight tracking-tighter text-white">{{ $agency->name }}</h1>
                <p class="text-xs font-medium uppercase tracking-widest text-slate-400">{{ $agency->city }}</p>
            </div>
        </div>

        <nav class="flex-1 space-y-1">
            <a class="flex items-center space-x-3 rounded-lg px-4 py-3 transition-all duration-300 ease-out {{ $isDashboardRoute ? 'bg-white/10 text-[#feb234]' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"
                href="{{ route('agency.dashboard', $agency->slug) }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-sm font-semibold">Dashboard</span>
            </a>

            <a class="flex items-center justify-between rounded-lg px-4 py-3 transition-all duration-300 ease-out {{ $isVehiclesRoute ? 'bg-white/10 text-[#feb234]' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"
                href="{{ route('agency.vehicles.manage') }}">
                <div class="flex items-center space-x-3">
                    <span class="material-symbols-outlined">directions_car</span>
                    <span class="text-sm font-semibold">Vehicles</span>
                </div>
                <span class="rounded bg-secondary px-2 py-0.5 text-[10px] font-bold text-white">{{ $fleetCount }}</span>
            </a>

            <a class="flex items-center justify-between rounded-lg px-4 py-3 transition-all duration-300 ease-out {{ $isReservationsRoute ? 'bg-white/10 text-[#feb234]' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"
                href="{{ route('agency.reservations.index', $agency->slug) }}">
                <div class="flex items-center space-x-3">
                    <span class="material-symbols-outlined">calendar_today</span>
                    <span class="text-sm font-semibold">Reservations</span>
                </div>
                <span class="rounded bg-error px-2 py-0.5 text-[10px] font-bold text-white">{{ $pendingReservationsCount }}</span>
            </a>

            <a class="flex items-center justify-between rounded-lg px-4 py-3 transition-all duration-300 ease-out {{ $isNotificationsRoute ? 'bg-white/10 text-[#feb234]' : 'text-slate-400 hover:bg-white/5 hover:text-white' }}"
                href="{{ route('notifications.index') }}">
                <div class="flex items-center space-x-3">
                    <span class="material-symbols-outlined">notifications</span>
                    <span class="text-sm font-semibold">Notifications</span>
                </div>
                <span class="rounded bg-secondary-container px-2 py-0.5 text-[10px] font-bold text-on-secondary-container">{{ $unreadNotificationsCount }}</span>
            </a>
        </nav>

        <div class="mt-6 border-t border-white/5 pt-6">
            <div class="mb-6 flex items-center space-x-3 px-4">
                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary-fixed text-sm font-bold text-primary-container">
                    @if (auth()->user()->profile_photo)
                        <img class="h-full w-full rounded-xl object-cover"
                            src="{{ Storage::url(auth()->user()->profile_photo) }}" alt="Avatar">
                    @else
                        {{ strtoupper(substr(auth()->user()->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr(auth()->user()->last_name ?? '', 0, 1)) }}
                    @endif
                </div>
                <div>
                    <p class="text-sm font-semibold text-white">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</p>
                    <p class="text-xs text-slate-500">Agency Owner</p>
                </div>
            </div>

            <a class="flex items-center space-x-3 px-4 py-3 text-slate-400 transition-all hover:text-white"
                href="{{ route('agency.edit', $agency->slug) }}">
                <span class="material-symbols-outlined">settings</span>
                <span class="text-sm">Settings</span>
            </a>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="flex w-full items-center space-x-3 px-4 py-3 text-error/80 transition-all hover:text-error">
                    <span class="material-symbols-outlined">logout</span>
                    <span class="text-sm font-medium">Logout</span>
                </button>
            </form>
        </div>
    </aside>

    <main class="ml-64 min-h-screen">
        <header class="fixed left-64 right-0 top-0 z-40 flex h-16 items-center justify-between bg-white/80 px-8 backdrop-blur-md">
            <div class="flex flex-col">
                <nav class="flex items-center space-x-2 text-[10px] font-bold uppercase tracking-widest text-on-surface-variant">
                    <span>Agency</span>
                    <span class="material-symbols-outlined text-[12px]">chevron_right</span>
                    <span class="text-secondary">{{ $pageBreadcrumb }}</span>
                </nav>
                <h2 class="text-xl font-extrabold tracking-tight text-primary-container">{{ $pageHeading }}</h2>
            </div>

            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-3 rounded-lg bg-surface-container-high px-4 py-2">
                    <span class="material-symbols-outlined text-sm text-secondary">calendar_month</span>
                    <span class="text-xs font-semibold text-on-surface-variant">{{ now()->format('F Y') }}</span>
                </div>

                @if (isset($headerActions))
                    {{ $headerActions }}
                @endif
            </div>
        </header>

        <div class="space-y-8 px-8 pb-12 pt-24">
            {{ $slot }}
        </div>
    </main>

    @if (isset($scripts))
        {{ $scripts }}
    @endif
</x-app-layout>
