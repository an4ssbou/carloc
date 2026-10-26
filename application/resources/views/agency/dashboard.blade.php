<x-agency-dashboard-layout
    :agency="$agency"
    page-title="Agency Dashboard"
    page-heading="Overview"
    page-breadcrumb="Dashboard"
    :fleet-count="$fleetTotal"
    :pending-reservations-count="$pendingReservations"
>
    <x-slot name="headerActions">
        <div class="flex items-center space-x-4">
            <div class="relative cursor-pointer transition-transform hover:scale-105">
                <span class="material-symbols-outlined text-on-surface-variant">notifications</span>
                @if ($pendingReservations > 0)
                    <span class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-error text-[8px] font-bold text-white">{{ $pendingReservations }}</span>
                @endif
            </div>
            <a href="{{ route('vehicles.create') }}"
                class="gold-gradient rounded-md px-5 py-2 text-sm font-bold text-white shadow-lg transition-all hover:brightness-110 active:scale-95">
                Add Vehicle
            </a>
        </div>
    </x-slot>

            @if (session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-5 py-4 text-sm font-medium text-emerald-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm font-medium text-rose-700">
                    {{ session('error') }}
                </div>
            @endif

            <!-- ✅ KPI Row — données dynamiques -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <!-- Total Revenue -->
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_20px_rgba(13,33,55,0.04)] hover:translate-y-[-4px] transition-transform duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-secondary/10 p-3 rounded-lg">
                            <span class="material-symbols-outlined text-secondary">payments</span>
                        </div>

                    </div>
                    <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wider">Total Revenue</p>
                    <h3 class="text-2xl font-extrabold text-primary-container mt-1">
                        {{ number_format($totalRevenue, 0, ',', ' ') }}
                        <span class="text-sm font-medium">DH</span>
                    </h3>
                </div>

                <!-- Active Reservations -->
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_20px_rgba(13,33,55,0.04)] hover:translate-y-[-4px] transition-transform duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-primary-fixed/30 p-3 rounded-lg">
                            <span class="material-symbols-outlined text-primary-fixed-variant">event_available</span>
                        </div>
                        <span class="text-secondary text-xs font-bold bg-secondary/10 px-2 py-1 rounded">
                            +{{ $pendingReservations }} pending
                        </span>
                    </div>
                    <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wider">Active Reservations
                    </p>
                    <h3 class="text-2xl font-extrabold text-primary-container mt-1">
                        {{ str_pad($activeReservations, 2, '0', STR_PAD_LEFT) }}
                        <span class="text-sm font-medium">Current</span>
                    </h3>
                </div>

                <!-- Fleet Availability -->
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_20px_rgba(13,33,55,0.04)] hover:translate-y-[-4px] transition-transform duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-tertiary-fixed-dim/20 p-3 rounded-lg">
                            <span class="material-symbols-outlined text-on-tertiary-container">directions_car</span>
                        </div>
                        @if ($fleetTotal > 0)
                            <div class="w-12 h-1.5 bg-surface-container rounded-full mt-2 overflow-hidden">
                                <div class="bg-tertiary-fixed-dim h-full transition-all"
                                    style="width: {{ $fleetTotal > 0 ? round(($fleetAvailable / $fleetTotal) * 100) : 0 }}%">
                                </div>
                            </div>
                        @endif
                    </div>
                    <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wider">Fleet Availability</p>
                    <h3 class="text-2xl font-extrabold text-primary-container mt-1">
                        {{ $fleetAvailable }}
                        <span class="text-sm font-medium text-on-surface-variant">/ {{ $fleetTotal }} Vehicles</span>
                    </h3>
                </div>

                <!-- Completed Reservations -->
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_4px_20px_rgba(13,33,55,0.04)] hover:translate-y-[-4px] transition-transform duration-300">
                    <div class="flex justify-between items-start mb-4">
                        <div class="bg-secondary-fixed/50 p-3 rounded-lg">
                            <span class="material-symbols-outlined text-secondary"
                                style="font-variation-settings: 'FILL' 1;">task_alt</span>
                        </div>
                    </div>
                    <p class="text-on-surface-variant text-xs font-bold uppercase tracking-wider">Completed Reservations
                    </p>
                    <h3 class="text-2xl font-extrabold text-primary-container mt-1">
                        {{ str_pad($completedReservations, 2, '0', STR_PAD_LEFT) }}
                        <span class="text-sm font-medium text-secondary">Closed</span>
                    </h3>
                </div>

            </div>

            <!-- ✅ Charts Row 1 : Revenue + Fleet Donut -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Revenue Area Chart -->
                <div
                    class="lg:col-span-2 bg-surface-container-lowest p-8 rounded-2xl shadow-[0_8px_30px_rgba(13,33,55,0.02)]">
                    <div class="flex justify-between items-center mb-10">
                        <div>
                            <h3 class="text-lg font-extrabold text-primary-container">Revenue Overview</h3>
                            <p class="text-on-surface-variant text-sm">Comparison between current and previous month
                            </p>
                        </div>
                        <div class="flex space-x-4">
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-secondary"></span>
                                <span class="text-xs font-semibold">This Month</span>
                            </div>
                            <div class="flex items-center space-x-2">
                                <span class="w-3 h-3 rounded-full bg-surface-variant"></span>
                                <span class="text-xs font-semibold">Last Month</span>
                            </div>
                        </div>
                    </div>

                    {{-- ✅ Chart.js remplace le SVG statique --}}
                    <div class="h-64 w-full relative">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>

                <!-- Fleet Status Donut -->
                <div class="bg-primary-container p-8 rounded-2xl text-white relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-lg font-bold">Fleet Status</h3>
                        <p class="text-slate-400 text-sm mb-8">Real-time tracking</p>

                        {{-- ✅ Chart.js remplace le SVG statique --}}
                        <div class="flex justify-center mb-8">
                            <div class="relative w-40 h-40 flex items-center justify-center">
                                <canvas id="fleetChart"></canvas>
                                <div class="absolute text-center pointer-events-none">
                                    <span class="block text-2xl font-extrabold text-white">{{ $fleetTotal }}</span>
                                    <span class="text-[10px] uppercase tracking-widest text-slate-400">Total</span>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div class="flex justify-between items-center text-sm">
                                <div class="flex items-center space-x-3">
                                    <span class="w-2 h-2 rounded-full bg-secondary"></span>
                                    <span class="text-slate-300">On Rental</span>
                                </div>
                                <span class="font-bold">{{ str_pad($fleetOnRental, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <div class="flex justify-between items-center text-sm">
                                <div class="flex items-center space-x-3">
                                    <span class="w-2 h-2 rounded-full bg-tertiary-fixed-dim"></span>
                                    <span class="text-slate-300">Available</span>
                                </div>
                                <span class="font-bold">{{ str_pad($fleetAvailable, 2, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- ✅ Charts Row 2 : Bar reservations + Top Vehicles -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- Bar Chart Reservations par jour -->
                <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-[0_8px_30px_rgba(13,33,55,0.02)]">
                    <h3 class="text-lg font-extrabold text-primary-container mb-6">Reservations This Week</h3>

                    {{-- ✅ Chart.js remplace les barres CSS hardcodées --}}
                    <div class="h-56 px-4">
                        <canvas id="reservationsChart"></canvas>
                    </div>
                </div>

                <!-- Top Performing Vehicles -->
                <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-[0_8px_30px_rgba(13,33,55,0.02)]">
                    <h3 class="text-lg font-extrabold text-primary-container mb-6">Top Performing Vehicles</h3>
                    <div class="space-y-6">
                        @forelse($topVehicles as $vehicle)
                            <div class="group cursor-pointer">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-4">
                                        @if ($vehicle->photo)
                                            <img class="w-12 h-12 rounded-lg object-cover"
                                                src="{{ Storage::url($vehicle->photo) }}"
                                                alt="{{ $vehicle->brand . ' ' . $vehicle->model }}" />
                                        @else
                                            <div
                                                class="w-12 h-12 rounded-lg bg-surface-container flex items-center justify-center">
                                                <span
                                                    class="material-symbols-outlined text-on-surface-variant">directions_car</span>
                                            </div>
                                        @endif
                                        <div>
                                            <p class="text-sm font-bold text-primary-container">
                                                {{ $vehicle->brand . ' ' . $vehicle->model }}</p>
                                            <p
                                                class="text-[10px] text-on-surface-variant uppercase tracking-widest font-bold">
                                                {{ $vehicle->fuel_type ?? '' }}</p>
                                        </div>
                                    </div>
                                    <span class="text-sm font-extrabold text-secondary">
                                        {{ number_format($vehicle->reservations_sum_total_price ?? 0, 0, ',', ' ') }}
                                        DH
                                    </span>
                                </div>
                                @php
                                    $maxRevenue = $topVehicles->max('reservations_sum_total_price') ?: 1;
                                    $percent = round(
                                        (($vehicle->reservations_sum_total_price ?? 0) / $maxRevenue) * 100,
                                    );
                                @endphp
                                <div class="w-full h-1.5 bg-surface-container rounded-full overflow-hidden">
                                    <div class="h-full gold-gradient transition-all group-hover:opacity-80"
                                        style="width: {{ $percent }}%">
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-on-surface-variant text-sm">Aucun véhicule pour le moment.</p>
                        @endforelse
                    </div>
                </div>

            </div>

            <!-- Recent Reservations Table -->
            <div
                id="recent-reservations"
                class="bg-surface-container-lowest rounded-2xl shadow-[0_8px_30px_rgba(13,33,55,0.02)] overflow-hidden">
                <div class="p-8 flex justify-between items-center">
                    <h3 class="text-lg font-extrabold text-primary-container">Recent Reservations</h3>
                    <a href="{{ route('agency.reservations.index', $agency->slug) }}"
                        class="text-secondary text-sm font-bold hover:underline">
                        Voir tout
                    </a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr
                                class="bg-surface-container-low text-[10px] uppercase tracking-widest font-bold text-on-surface-variant">
                                <th class="px-8 py-4">Client</th>
                                <th class="px-8 py-4">Vehicle</th>
                                <th class="px-8 py-4">Dates</th>
                                <th class="px-8 py-4">Status</th>
                                <th class="px-8 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-surface-variant/20">
                            @forelse($recentReservations as $reservation)
                                <tr class="hover:bg-surface-container-low/50 transition-colors">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center space-x-3">
                                            <div
                                                class="w-10 h-10 rounded-full bg-primary-fixed text-primary-container flex items-center justify-center font-bold text-sm">
                                                {{ strtoupper(substr($reservation->client->first_name ?? 'U', 0, 1)) }}{{ strtoupper(substr($reservation->client->last_name ?? '', 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="font-bold">{{ $reservation->client?->first_name }}
                                                    {{ $reservation->client?->last_name }}</p>
                                                <p class="text-xs text-on-surface-variant">
                                                    {{ $reservation->client?->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="font-semibold">
                                            {{ $reservation->vehicle ? $reservation->vehicle->brand . ' ' . $reservation->vehicle->model : 'N/A' }}
                                        </p>
                                        <p class="text-xs text-on-surface-variant">
                                            {{ $reservation->vehicle->registration_number ?? '' }}</p>
                                    </td>
                                    <td class="px-8 py-6">
                                        <p class="font-medium">
                                            {{ \Carbon\Carbon::parse($reservation->start_date)->format('M d') }}
                                            -
                                            {{ \Carbon\Carbon::parse($reservation->end_date)->format('M d') }}
                                        </p>
                                        <p class="text-xs text-on-surface-variant">
                                            {{ \Carbon\Carbon::parse($reservation->start_date)->diffInDays($reservation->end_date) }}
                                            Days
                                        </p>
                                    </td>
                                    <td class="px-8 py-6">
                                        @php
                                            $statusClasses = match ($reservation->status) {
                                                'confirmed' => 'bg-tertiary-fixed-dim/20 text-on-tertiary-container',
                                                'pending' => 'bg-secondary-container/20 text-on-secondary-container',
                                                'cancelled' => 'bg-error/10 text-error',
                                                default => 'bg-surface-container text-on-surface-variant',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold {{ $statusClasses }}">
                                            {{ ucfirst($reservation->status) }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <div class="flex justify-end gap-2">
                                            @if ($reservation->status === 'pending')
                                                <form method="POST" action="{{ route('agency.reservations.confirm', [$agency->slug, $reservation]) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="rounded-lg bg-emerald-600 px-3 py-2 text-xs font-bold text-white transition hover:bg-emerald-500">
                                                        Confirmer
                                                    </button>
                                                </form>
                                            @endif

                                            @if (in_array($reservation->status, ['pending', 'confirmed'], true))
                                                <form method="POST" action="{{ route('agency.reservations.cancel', [$agency->slug, $reservation]) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-bold text-rose-700 transition hover:bg-rose-100">
                                                        Annuler
                                                    </button>
                                                </form>
                                            @else
                                                <span class="inline-flex items-center px-3 py-2 text-xs font-semibold text-on-surface-variant">
                                                    Aucune action
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-10 text-center text-on-surface-variant text-sm">
                                        Aucune réservation récente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Bottom Row: Activity + Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                <!-- Recent Activity -->
                <div class="bg-surface-container-lowest p-8 rounded-2xl shadow-[0_8px_30px_rgba(13,33,55,0.02)]">
                    <h3 class="text-lg font-extrabold text-primary-container mb-6">Recent Activity</h3>
                    <div class="space-y-6">
                        @forelse ($activityLog as $activity)
                            <div class="flex space-x-4">
                                <div class="w-2 h-2 mt-1.5 rounded-full {{ $activity['dot_color'] }} flex-shrink-0"></div>
                                <div>
                                    <p class="text-sm font-semibold">{{ $activity['message'] }}</p>
                                    <p class="text-xs text-on-surface-variant">{{ $activity['time_label'] }}</p>
                                </div>
                            </div>
                        @empty
                            <div class="rounded-xl border border-dashed border-outline/40 px-4 py-5">
                                <p class="text-sm font-semibold text-primary-container">Aucune activite recente</p>
                                <p class="text-xs text-on-surface-variant mt-1">Les nouvelles reservations et mises a jour apparaitront ici.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-primary-container p-8 rounded-2xl shadow-xl flex flex-col justify-between">
                    <h3 class="text-lg font-bold text-white mb-6">Quick Actions</h3>
                    <div class="grid grid-cols-2 gap-4">
                        <button
                            class="flex flex-col items-center justify-center p-4 bg-white/5 hover:bg-white/10 rounded-xl transition-all group">
                            <span
                                class="material-symbols-outlined text-secondary mb-2 group-hover:scale-110 transition-transform">add_circle</span>
                            <span class="text-xs text-white font-medium">New Booking</span>
                        </button>
                        <button
                            class="flex flex-col items-center justify-center p-4 bg-white/5 hover:bg-white/10 rounded-xl transition-all group">
                            <span
                                class="material-symbols-outlined text-secondary mb-2 group-hover:scale-110 transition-transform">file_download</span>
                            <span class="text-xs text-white font-medium">Export Reports</span>
                        </button>
                        <button
                            class="flex flex-col items-center justify-center p-4 bg-white/5 hover:bg-white/10 rounded-xl transition-all group">
                            <span
                                class="material-symbols-outlined text-secondary mb-2 group-hover:scale-110 transition-transform">support_agent</span>
                            <span class="text-xs text-white font-medium">Contact Support</span>
                        </button>
                    </div>
                </div>

            </div>
    <x-slot name="scripts">
    @php($chartData = compact('revenueThisMonth', 'revenueLastMonth', 'joursLabels', 'dataReservationsJour', 'fleetOnRental', 'fleetAvailable'))
    <script id="agency-chart-data" type="application/json">@json($chartData)</script>
    @vite('resources/js/agency-charts.js')
    </x-slot>

</x-agency-dashboard-layout>
