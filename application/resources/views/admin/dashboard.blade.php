<x-admin-layout :pending-agencies-count="$pendingAgenciesCount" title="Admin Dashboard">
    @php
        $adminUser = auth()->user();
        $revenueMax = max(
            collect($revenueOverview)->max(fn ($item) => ($item['commissions'] ?? 0) + ($item['extra_fees'] ?? 0)),
            1,
        );
        $recentUsersList = collect($recentUsers);
        $recentUsersOverflow = max($kpis['totalUsers'] - $recentUsersList->count(), 0);
        $activityItems = collect($activityLog);
        $rejectAgencyId = old('reject_agency_id');
        $availablePercentage = $vehicleDistribution['percentages']['available'] ?? 0;
        $unavailablePercentage = $vehicleDistribution['percentages']['unavailable'] ?? 0;
        $donutStyle = 'background: conic-gradient(#1c3f63 0% ' . $availablePercentage . '%, #feb234 ' . $availablePercentage . '% 100%);';
    @endphp
    <!-- Main Content Area -->
    <main class="ml-64 min-h-screen">
        <!-- TopAppBar -->
        <header
            class="sticky top-0 z-40 w-full bg-white/80 backdrop-blur-md flex justify-between items-center h-16 px-8 font-['Inter'] text-sm font-medium">
            <div class="flex items-center gap-4 text-slate-500">
                <span>Admin Dashboard</span>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="text-[#815500] font-bold">Overview</span>
            </div>
            <div class="flex items-center gap-6">
                <div
                    class="flex items-center bg-surface-container-high rounded-lg px-3 py-1.5 gap-2 text-on-surface-variant">
                    <span class="material-symbols-outlined text-lg">calendar_today</span>
                    <span>{{ $dateRange['label'] }}</span>
                </div>
                <div class="flex items-center gap-4 border-l border-surface-container-high pl-6">
                    <button
                        class="relative text-slate-500 hover:text-[#0D2137] transition-colors opacity-80 hover:opacity-100">
                        <span class="material-symbols-outlined">notifications</span>
                        <span
                            class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-error ring-2 ring-white"></span>
                    </button>
                    <button class="text-slate-500 hover:text-[#0D2137] transition-colors opacity-80 hover:opacity-100">
                        <span class="material-symbols-outlined">help</span>
                    </button>
                    <div class="flex items-center gap-3 ml-2">
                        <img alt="Admin User Avatar" class="w-8 h-8 rounded-full object-cover"
                            src="{{ $adminUser?->profile_photo_url }}" />
                    </div>
                </div>
            </div>
        </header>
        <!-- Dashboard Content -->
        <div class="p-8 space-y-8">
            @if (session('success'))
                <div class="rounded-xl border border-green-200 bg-green-50 px-5 py-4 text-sm font-medium text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-xl border border-red-200 bg-red-50 px-5 py-4 text-sm font-medium text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Pending Approvals Banner -->
            <div
                class="bg-secondary-fixed/30 border-l-4 border-secondary p-5 flex items-center justify-between rounded-r-xl shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-secondary/10 rounded-full flex items-center justify-center text-secondary">
                        <span class="material-symbols-outlined" style="font-variation-settings: 'FILL' 1;">info</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-on-secondary-container tracking-tight">
                            {{ $pendingAgenciesCount }} agencies waiting for approval
                        </h4>
                        <p class="text-sm text-on-secondary-container/70">Please review the new partner requests to
                            maintain service expansion.</p>
                    </div>
                </div>
                <a href="#pending-agency-approvals"
                    class="px-6 py-2 bg-gradient-to-br from-secondary to-secondary-container text-white font-bold rounded-md hover:scale-105 transition-all duration-300 shadow-sm">
                    Review Now
                </a>
            </div>
            <!-- KPI Grid -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
                <!-- Card 1 -->
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] group hover:translate-y-[-4px] transition-all duration-300">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total Revenue</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-2xl font-black text-primary-container">{{ number_format($kpis['totalRevenue'], 0, '.', ',') }} <span
                                class="text-sm font-medium">MAD</span></h3>
                        <span
                            class="text-tertiary-fixed-variant text-xs font-bold flex items-center px-2 py-1 rounded"></span>
                    </div>
                </div>
                <!-- Card 2 -->
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] group hover:translate-y-[-4px] transition-all duration-300">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total Agencies</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-2xl font-black text-primary-container">{{ $kpis['activeAgencies'] }} <span
                                class="text-sm font-medium">Active</span></h3>
                        <span class="text-secondary text-xs font-bold px-2 py-1 bg-secondary-fixed/30 rounded">{{ $kpis['pendingAgencies'] }}
                            Pending</span>
                    </div>
                </div>
                <!-- Card 3 -->
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] group hover:translate-y-[-4px] transition-all duration-300">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total Users</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-2xl font-black text-primary-container">{{ $kpis['totalUsers'] }}</h3>
                        <span class="material-symbols-outlined text-slate-300">group</span>
                    </div>
                </div>
                <!-- Card 4 -->
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] group hover:translate-y-[-4px] transition-all duration-300">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Total Vehicles</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-2xl font-black text-primary-container">{{ $kpis['totalVehicles'] }}</h3>
                        <span class="material-symbols-outlined text-slate-300">directions_car</span>
                    </div>
                </div>
                <!-- Card 5 -->
                <div
                    class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] group hover:translate-y-[-4px] transition-all duration-300">
                    <p class="text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Reservations</p>
                    <div class="flex items-end justify-between">
                        <h3 class="text-2xl font-black text-primary-container">{{ $kpis['totalReservations'] }}</h3>
                        <span class="material-symbols-outlined text-slate-300">calendar_month</span>
                    </div>
                </div>
            </div>
            <!-- Main Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div
                    class="lg:col-span-2 bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
                    <div class="flex items-center justify-between mb-8">
                        <div>
                            <h2 class="text-xl font-black text-primary-container tracking-tight">Platform Revenue
                                Overview</h2>
                            <p class="text-sm text-slate-500">Monthly breakdown of agency commissions and platform fees.
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <span class="flex items-center gap-1.5 text-xs font-semibold"><span
                                    class="w-3 h-3 rounded-full bg-primary-container"></span> Commissions</span>
                            <span class="flex items-center gap-1.5 text-xs font-semibold"><span
                                    class="w-3 h-3 rounded-full bg-secondary-container"></span> Extra Fees</span>
                        </div>
                    </div>
                    <!-- Mock Area Chart -->
                    <div class="h-64 flex items-end gap-1 px-2 relative border-b border-surface-container-high">
                        @foreach ($revenueOverview as $month)
                            @php
                                $totalMonthRevenue = $month['commissions'] + $month['extra_fees'];
                                $barHeight = max(($totalMonthRevenue / $revenueMax) * 100, 8);
                                $feeHeight = $totalMonthRevenue > 0 ? max(($month['extra_fees'] / $totalMonthRevenue) * 100, 8) : 0;
                            @endphp
                            <div class="flex-1 bg-primary-container/20 rounded-t-sm relative group"
                                style="height: {{ $barHeight }}%;">
                                <div class="absolute inset-x-0 bottom-0 bg-secondary-container/50 rounded-t-sm"
                                    style="height: {{ $feeHeight }}%;"></div>
                                <div
                                    class="hidden group-hover:block absolute -top-8 left-1/2 -translate-x-1/2 bg-primary-container text-white text-[10px] px-2 py-1 rounded whitespace-nowrap">
                                    {{ number_format($totalMonthRevenue, 0, '.', ',') }} MAD
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div
                        class="flex justify-between mt-4 px-2 text-[10px] font-bold text-slate-400 uppercase tracking-tighter">
                        @foreach ($revenueOverview as $month)
                            <span>{{ $month['label'] }}</span>
                        @endforeach
                    </div>
                </div>
                <div
                    class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] flex flex-col">
                    <h2 class="text-xl font-black text-primary-container tracking-tight mb-2">Platform Status</h2>
                    <p class="text-sm text-slate-500 mb-8">Vehicle availability distribution.</p>
                    <div class="flex-1 flex flex-col items-center justify-center relative">
                        <!-- Donut Chart UI -->
                        <div class="relative flex items-center justify-center w-48 h-48 rounded-full"
                            style="{{ $donutStyle }}">
                            <div class="flex h-32 w-32 flex-col items-center justify-center rounded-full bg-white">
                                <span class="text-3xl font-black text-primary-container">{{ $vehicleDistribution['total'] }}</span>
                                <span class="text-[10px] font-bold text-slate-400 uppercase">Vehicles</span>
                            </div>
                        </div>
                        <div class="mt-8 w-full space-y-3">
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2"><span
                                        class="w-3 h-3 rounded-full bg-primary-container"></span> <span
                                        class="font-medium">Available</span></div>
                                <span class="font-bold">{{ $vehicleDistribution['percentages']['available'] }}%</span>
                            </div>
                            <div class="flex items-center justify-between text-sm">
                                <div class="flex items-center gap-2"><span
                                        class="w-3 h-3 rounded-full bg-secondary-container"></span> <span
                                        class="font-medium">Unavailable</span></div>
                                <span class="font-bold">{{ $vehicleDistribution['percentages']['unavailable'] }}%</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Bottom Panels -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Pending Agencies -->
                <div id="pending-agency-approvals"
                    class="bg-surface-container-lowest rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] overflow-hidden">
                    <div class="p-6 border-b border-surface-container-low flex justify-between items-center">
                        <h2 class="text-lg font-black text-primary-container tracking-tight">Pending Agency Approvals
                        </h2>
                        <span class="text-xs font-bold text-secondary bg-secondary-fixed/30 px-2 py-1 rounded">{{ $pendingAgenciesCount }}
                            Pending</span>
                    </div>
                    <div class="divide-y divide-surface-container-low">
                        @forelse ($pendingAgencies as $agency)
                            <div
                                class="p-5 flex items-center justify-between hover:bg-surface-container-low/30 transition-colors">
                                <div class="flex items-center gap-4">
                                    <img alt="{{ $agency->name }}" class="w-12 h-12 rounded-lg object-cover"
                                        src="{{ $agency->logo_url }}" />
                                    <div>
                                        <h5 class="font-bold text-primary-container">{{ $agency->name }}</h5>
                                        <p class="text-xs text-slate-500">{{ $agency->city }} • {{ $agency->vehicles_count }}
                                            Vehicles</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form method="POST" action="{{ route('admin.agencies.approve', $agency) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="p-2 text-on-tertiary-container hover:bg-tertiary-fixed/20 rounded-md transition-colors"
                                            title="Approuver {{ $agency->name }}">
                                            <span class="material-symbols-outlined">check_circle</span>
                                        </button>
                                    </form>
                                    <button type="button"
                                        class="p-2 text-error hover:bg-error-container rounded-md transition-colors"
                                        title="Refuser {{ $agency->name }}"
                                        data-open-modal="reject-agency-{{ $agency->id }}">
                                        <span class="material-symbols-outlined">cancel</span>
                                    </button>
                                </div>
                            </div>

                            <div id="reject-agency-{{ $agency->id }}"
                                class="fixed inset-0 z-[60] hidden items-center justify-center bg-slate-900/60 px-4"
                                data-modal-root>
                                <div class="absolute inset-0" data-close-modal></div>
                                <div class="relative z-10 w-full max-w-lg rounded-2xl bg-white p-6 shadow-2xl">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <h3 class="text-lg font-black text-primary-container">Refuser l'agence</h3>
                                            <p class="mt-1 text-sm text-slate-500">
                                                Indique la raison du refus pour <span
                                                    class="font-semibold text-primary-container">{{ $agency->name }}</span>.
                                            </p>
                                        </div>
                                        <button type="button" class="text-slate-400 hover:text-slate-700"
                                            data-close-modal>
                                            <span class="material-symbols-outlined">close</span>
                                        </button>
                                    </div>

                                    <form method="POST" action="{{ route('admin.agencies.reject', $agency) }}"
                                        class="mt-6 space-y-4">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="reject_agency_id" value="{{ $agency->id }}">

                                        <div>
                                            <label for="rejection_reason_{{ $agency->id }}"
                                                class="mb-2 block text-sm font-semibold text-primary-container">
                                                Raison du refus
                                            </label>
                                            <textarea id="rejection_reason_{{ $agency->id }}" name="rejection_reason" rows="5"
                                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-700 shadow-sm focus:border-secondary focus:outline-none focus:ring-2 focus:ring-secondary/20"
                                                placeholder="Explique pourquoi la demande est refusée...">{{ (string) ($rejectAgencyId == $agency->id ? old('rejection_reason') : '') }}</textarea>
                                            @if ($rejectAgencyId == $agency->id)
                                                @error('rejection_reason')
                                                    <p class="mt-2 text-sm font-medium text-red-600">{{ $message }}</p>
                                                @enderror
                                            @endif
                                        </div>

                                        <div class="flex justify-end gap-3">
                                            <button type="button"
                                                class="rounded-xl border border-slate-200 px-4 py-2 text-sm font-semibold text-slate-600 transition hover:bg-slate-50"
                                                data-close-modal>
                                                Annuler
                                            </button>
                                            <button type="submit"
                                                class="rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-red-700">
                                                Confirmer le refus
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-5 text-sm text-slate-500">
                                No agency approval requests at the moment.
                            </div>
                        @endforelse
                    </div>
                    <a class="block p-4 text-center text-xs font-bold text-[#815500] hover:bg-surface-container-low transition-colors uppercase tracking-widest"
                        href="{{ route('admin.agencies') }}">View All Requests</a>
                </div>
                <!-- Recent Activity & Users Grid -->
                <div class="space-y-8">
                    <!-- Recent Users -->
                    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-lg font-black text-primary-container tracking-tight">Recent Users</h2>
                            <button class="text-xs font-bold text-secondary hover:underline"><a href="{{ route('admin.users') }}">View All</a></button>
                        </div>
                        <div class="flex -space-x-3 mb-6">
                            @foreach ($recentUsersList->take(3) as $recentUser)
                                <img alt="{{ $recentUser['name'] }}"
                                    class="w-10 h-10 rounded-full border-4 border-white object-cover"
                                    src="{{ $recentUser['profile_photo_url'] }}" />
                            @endforeach
                            @if ($recentUsersOverflow > 0)
                                <div
                                    class="w-10 h-10 rounded-full border-4 border-white bg-surface-container-high flex items-center justify-center text-[10px] font-bold">
                                    +{{ $recentUsersOverflow }}</div>
                            @endif
                        </div>
                        <div class="space-y-4">
                            @forelse ($recentUsersList as $index => $recentUser)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-full {{ $index % 2 === 0 ? 'bg-primary-container text-white' : 'bg-secondary-container text-primary-container' }} flex items-center justify-center text-xs font-bold">
                                            {{ $recentUser['initials'] }}</div>
                                        <div>
                                            <p class="text-sm font-bold text-primary-container">{{ $recentUser['name'] }}</p>
                                            <p class="text-[10px] text-slate-500">Joined {{ $recentUser['joined_since'] }}</p>
                                        </div>
                                    </div>
                                    <span
                                        class="px-2 py-0.5 rounded {{ $recentUser['is_verified'] ? 'bg-tertiary-fixed/20 text-on-tertiary-container' : 'bg-surface-container-high text-slate-500' }} text-[10px] font-bold">{{ $recentUser['status_label'] }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No recent users to display.</p>
                            @endforelse
                        </div>
                    </div>
                    <!-- Activity Log -->
                    <div class="bg-surface-container-lowest p-6 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
                        <h2 class="text-lg font-black text-primary-container tracking-tight mb-6">Activity Log</h2>
                        <div
                            class="space-y-6 relative before:absolute before:left-3.5 before:top-2 before:bottom-2 before:w-[1px] before:bg-surface-container-high">
                            @forelse ($activityItems as $activity)
                                <div class="relative flex gap-6">
                                    <div
                                        class="w-7 h-7 rounded-full {{ $activity['icon_background'] }} flex items-center justify-center z-10 {{ $activity['icon_color'] }}">
                                        <span class="material-symbols-outlined text-sm"
                                            style="font-variation-settings: 'FILL' 1;">{{ $activity['icon'] }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-primary-container">{{ $activity['message'] }}</p>
                                        <p class="text-[10px] text-slate-400">{{ $activity['time_label'] }}</p>
                                    </div>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500">No recent activity found.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="p-8 text-center text-slate-400 text-xs font-medium">
            © {{ now()->year }} CarLoc Platform. All rights reserved. Built for Elite Management.
        </footer>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const openModal = (modal) => {
                if (!modal) return;
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
            };

            const closeModal = (modal) => {
                if (!modal) return;
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.classList.remove('overflow-hidden');
            };

            document.querySelectorAll('[data-open-modal]').forEach((button) => {
                button.addEventListener('click', () => {
                    openModal(document.getElementById(button.dataset.openModal));
                });
            });

            document.querySelectorAll('[data-modal-root]').forEach((modal) => {
                modal.querySelectorAll('[data-close-modal]').forEach((button) => {
                    button.addEventListener('click', () => closeModal(modal));
                });
            });

            document.addEventListener('keydown', (event) => {
                if (event.key !== 'Escape') return;

                document.querySelectorAll('[data-modal-root]').forEach((modal) => {
                    if (!modal.classList.contains('hidden')) {
                        closeModal(modal);
                    }
                });
            });

            @if ($rejectAgencyId)
                openModal(document.getElementById('reject-agency-{{ $rejectAgencyId }}'));
            @endif
        });
    </script>
</x-admin-layout>
