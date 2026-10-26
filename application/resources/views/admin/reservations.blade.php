<x-admin-layout :pending-agencies-count="$pendingAgenciesCount" title="Reservations Management">
    <main class="ml-64 min-h-screen bg-surface">
        <header
            class="sticky top-0 z-40 flex h-16 w-full items-center justify-between bg-white/80 px-8 font-['Inter'] text-sm font-medium text-[#0D2137] backdrop-blur-md">
            <div class="flex flex-col">
                <nav class="mb-0.5 flex items-center space-x-2 text-xs text-slate-500">
                    <span>Dashboard</span>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="font-bold text-[#815500]">Reservations</span>
                </nav>
                <h2 class="text-lg font-extrabold tracking-tight">Reservations Management</h2>
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

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-5">
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Total Reservations</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $totalReservations }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-container/5 text-primary-container">
                        <span class="material-symbols-outlined text-2xl">calendar_month</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Pending</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $pendingReservationsCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 text-amber-700">
                        <span class="material-symbols-outlined text-2xl">pending</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Confirmed</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $confirmedReservationsCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-green-700">
                        <span class="material-symbols-outlined text-2xl">task_alt</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Completed</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $completedReservationsCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                        <span class="material-symbols-outlined text-2xl">check_circle</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Revenue</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ number_format($totalReservationsRevenue, 0, '.', ',') }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-secondary-fixed/30 text-secondary">
                        <span class="material-symbols-outlined text-2xl">payments</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 rounded-xl bg-surface-container-low p-3">
                <span class="px-3 py-2 text-xs font-black uppercase tracking-widest text-slate-500">Filter By Status</span>
                <a href="{{ route('admin.reservations') }}"
                    class="{{ $currentStatusFilter === 'all' ? 'bg-primary-container text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    All ({{ $totalReservations }})
                </a>
                <a href="{{ route('admin.reservations', ['status' => 'pending']) }}"
                    class="{{ $currentStatusFilter === 'pending' ? 'bg-amber-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Pending ({{ $pendingReservationsCount }})
                </a>
                <a href="{{ route('admin.reservations', ['status' => 'confirmed']) }}"
                    class="{{ $currentStatusFilter === 'confirmed' ? 'bg-green-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Confirmed ({{ $confirmedReservationsCount }})
                </a>
                <a href="{{ route('admin.reservations', ['status' => 'completed']) }}"
                    class="{{ $currentStatusFilter === 'completed' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Completed ({{ $completedReservationsCount }})
                </a>
                <a href="{{ route('admin.reservations', ['status' => 'cancelled']) }}"
                    class="{{ $currentStatusFilter === 'cancelled' ? 'bg-red-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Cancelled ({{ $cancelledReservationsCount }})
                </a>
            </div>

            <form method="GET" action="{{ route('admin.reservations') }}"
                class="rounded-xl bg-surface-container-low p-4 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-4">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by reference, client, agency or vehicle"
                        class="w-full rounded-lg border-none bg-white px-4 py-3 text-sm font-medium text-on-surface outline-none focus:ring-0">
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full rounded-lg border-none bg-white px-4 py-3 text-sm font-medium text-on-surface outline-none focus:ring-0">
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full rounded-lg border-none bg-white px-4 py-3 text-sm font-medium text-on-surface outline-none focus:ring-0">
                    <div class="flex gap-3">
                        <a href="{{ route('admin.reservations') }}"
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
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Reservation</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Client</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Agency / Vehicle</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Dates</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Amount</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Status</th>
                                <th class="px-6 py-5 text-right text-[10px] font-black uppercase tracking-widest text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container-low">
                            @forelse ($reservations as $reservation)
                                <tr class="hover:bg-surface-container-low/40">
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-bold text-primary-container">{{ $reservation->reference }}</span>
                                            <span class="text-[11px] text-slate-500">#{{ $reservation->id }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-medium text-on-surface">{{ $reservation->client?->full_name ?? 'Unknown client' }}</span>
                                            <span class="text-[11px] text-slate-400">{{ $reservation->client?->email }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-medium text-on-surface">{{ $reservation->agency?->name ?? 'Unknown agency' }}</span>
                                            <span class="text-[11px] text-slate-400">
                                                {{ trim(($reservation->vehicle?->brand ?? '') . ' ' . ($reservation->vehicle?->model ?? '')) }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1 text-[11px] text-slate-500">
                                            <span>{{ $reservation->start_date->format('M d, Y') }} → {{ $reservation->end_date->format('M d, Y') }}</span>
                                            <span>{{ $reservation->duration_days }} days</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="text-sm font-black text-secondary">{{ number_format($reservation->total_price, 2, '.', ',') }} MAD</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-tight
                                            {{ $reservation->status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                            {{ $reservation->status === 'confirmed' ? 'bg-green-100 text-green-700' : '' }}
                                            {{ $reservation->status === 'completed' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $reservation->status === 'cancelled' ? 'bg-red-100 text-red-700' : '' }}
                                            {{ $reservation->status === 'rejected' ? 'bg-slate-200 text-slate-700' : '' }}">
                                            {{ $reservation->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($reservation->status === 'pending')
                                                <form method="POST" action="{{ route('admin.reservations.confirm', $reservation) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="rounded-lg bg-green-100 px-3 py-2 text-xs font-bold text-green-700 transition hover:bg-green-200">
                                                        Confirm
                                                    </button>
                                                </form>
                                            @endif

                                            @if (in_array($reservation->status, ['pending', 'confirmed'], true))
                                                <form method="POST" action="{{ route('admin.reservations.cancel', $reservation) }}">
                                                    @csrf
                                                    <button type="submit"
                                                        class="rounded-lg bg-red-100 px-3 py-2 text-xs font-bold text-red-700 transition hover:bg-red-200">
                                                        Cancel
                                                    </button>
                                                </form>
                                            @else
                                                <span class="text-xs font-semibold text-slate-400">No actions</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-slate-500">
                                        No reservations found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl bg-surface-container-lowest px-6 py-4 shadow-[0_8px_30px_rgba(13,33,55,0.04)]">
                {{ $reservations->links() }}
            </div>
        </div>
    </main>
</x-admin-layout>
