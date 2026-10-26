<x-admin-layout :pending-agencies-count="$pendingAgenciesCount" title="Vehicles Management">
    <main class="ml-64 min-h-screen bg-surface">
        <header
            class="sticky top-0 z-40 flex h-16 w-full items-center justify-between bg-white/80 px-8 font-['Inter'] text-sm font-medium text-[#0D2137] backdrop-blur-md">
            <div class="flex flex-col">
                <nav class="mb-0.5 flex items-center space-x-2 text-xs text-slate-500">
                    <span>Dashboard</span>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="font-bold text-[#815500]">Vehicles</span>
                </nav>
                <h2 class="text-lg font-extrabold tracking-tight">Vehicles Management</h2>
            </div>
            <div class="flex items-center space-x-3 pl-6">
                <div class="text-right hidden sm:block">
                    <p class="text-[11px] font-bold text-[#0D2137]">{{ $adminUser?->full_name }}</p>
                    <p class="text-[10px] text-slate-500">Super Admin</p>
                </div>
                <img alt="Admin User Avatar" class="h-9 w-9 rounded-full border border-outline-variant/30 object-cover"
                    src="{{ $adminUser?->profile_photo_url }}" />
            </div>
        </header>

        <div class="mx-auto flex w-full max-w-[1600px] flex-col space-y-8 p-8">
            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-5">
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Total Vehicles</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $totalVehicles }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-container/5 text-primary-container">
                        <span class="material-symbols-outlined text-2xl">directions_car</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Available</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $availableVehiclesCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-green-700">
                        <span class="material-symbols-outlined text-2xl">check_circle</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Unavailable</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $unavailableVehiclesCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                        <span class="material-symbols-outlined text-2xl">car_rental</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Featured</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $featuredVehiclesCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-secondary-fixed/30 text-secondary">
                        <span class="material-symbols-outlined text-2xl">star</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">With GPS</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $gpsVehiclesCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                        <span class="material-symbols-outlined text-2xl">location_on</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 rounded-xl bg-surface-container-low p-3">
                <span class="px-3 py-2 text-xs font-black uppercase tracking-widest text-slate-500">Filter By Status</span>
                <a href="{{ route('admin.vehicles') }}"
                    class="{{ $currentStatusFilter === 'all' ? 'bg-primary-container text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    All ({{ $totalVehicles }})
                </a>
                <a href="{{ route('admin.vehicles', ['status' => 'available']) }}"
                    class="{{ $currentStatusFilter === 'available' ? 'bg-green-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Available ({{ $availableVehiclesCount }})
                </a>
                <a href="{{ route('admin.vehicles', ['status' => 'unavailable']) }}"
                    class="{{ $currentStatusFilter === 'unavailable' ? 'bg-amber-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Unavailable ({{ $unavailableVehiclesCount }})
                </a>
            </div>

            <form method="GET" action="{{ route('admin.vehicles') }}"
                class="rounded-xl bg-surface-container-low p-4 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by brand, model, registration or agency"
                        class="w-full rounded-lg border-none bg-white px-4 py-3 text-sm font-medium text-on-surface outline-none focus:ring-0 md:max-w-xl">
                    <div class="flex gap-3">
                        <a href="{{ route('admin.vehicles') }}"
                            class="rounded-lg px-4 py-3 text-sm font-bold text-on-surface-variant transition-colors hover:bg-white hover:text-on-surface">
                            Reset
                        </a>
                        <button type="submit"
                            class="gold-gradient rounded-lg px-6 py-3 text-sm font-black uppercase tracking-wider text-white transition-opacity hover:opacity-90">
                            Search
                        </button>
                    </div>
                </div>
            </form>

            <div class="overflow-hidden rounded-xl bg-surface-container-lowest shadow-[0_8px_30px_rgba(13,33,55,0.04)]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-surface-container-high">
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Vehicle</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Agency</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Specs</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Pricing</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container-low">
                            @forelse ($vehicles as $vehicle)
                                <tr class="hover:bg-surface-container-low/40">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            <img alt="{{ $vehicle->brand }} {{ $vehicle->model }}" class="h-12 w-12 rounded-lg object-cover"
                                                src="{{ $vehicle->primary_photo_url }}" />
                                            <div>
                                                <p class="text-sm font-bold text-primary-container">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                                                <p class="text-[11px] text-slate-500">{{ $vehicle->registration_number }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-medium text-on-surface">{{ $vehicle->agency?->name ?? 'No agency' }}</span>
                                            <span class="text-[11px] text-slate-400">{{ $vehicle->agency?->city }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1 text-[11px] text-slate-500">
                                            <span>{{ $vehicle->year }} • {{ $vehicle->transmission }}</span>
                                            <span>{{ $vehicle->fuel_type_label }} • {{ $vehicle->seats }} seats</span>
                                            <span>{{ $vehicle->has_gps ? 'GPS enabled' : 'No GPS' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-black text-secondary">{{ number_format($vehicle->price_per_day, 2, '.', ',') }} MAD/day</span>
                                            <span class="text-[11px] text-slate-400">
                                                {{ $vehicle->price_week ? number_format($vehicle->price_week, 2, '.', ',') . ' MAD/week' : 'No weekly rate' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-tight {{ $vehicle->status === 'available' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                                            {{ ucfirst($vehicle->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-sm text-slate-500">
                                        No vehicles found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl bg-surface-container-lowest px-6 py-4 shadow-[0_8px_30px_rgba(13,33,55,0.04)]">
                {{ $vehicles->links() }}
            </div>
        </div>
    </main>
</x-admin-layout>
