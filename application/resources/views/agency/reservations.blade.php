<x-agency-dashboard-layout
    :agency="$agency"
    page-title="Agency Reservations"
    page-heading="Mes Reservations"
    page-breadcrumb="Reservations"
    :fleet-count="$fleetTotal"
    :pending-reservations-count="$pendingReservations"
>
    @if (session('success'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-6 py-4 text-emerald-700">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-6 py-4 text-rose-700">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 xl:grid-cols-4">
        <div class="rounded-xl bg-surface-container-lowest p-6 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
            <p class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Pending</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-black text-primary-container">{{ $pendingReservations }}</h3>
                <span class="rounded bg-amber-100 px-2 py-1 text-xs font-bold text-amber-700">Action requise</span>
            </div>
        </div>
        <div class="rounded-xl bg-surface-container-lowest p-6 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
            <p class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Confirmed</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-black text-primary-container">{{ $confirmedReservations }}</h3>
                <span class="rounded bg-emerald-100 px-2 py-1 text-xs font-bold text-emerald-700">Validees</span>
            </div>
        </div>
        <div class="rounded-xl bg-surface-container-lowest p-6 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
            <p class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Completed</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-black text-primary-container">{{ $completedReservations }}</h3>
                <span class="rounded bg-sky-100 px-2 py-1 text-xs font-bold text-sky-700">Terminees</span>
            </div>
        </div>
        <div class="rounded-xl bg-surface-container-lowest p-6 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
            <p class="mb-2 text-xs font-bold uppercase tracking-widest text-slate-400">Cancelled</p>
            <div class="flex items-end justify-between">
                <h3 class="text-2xl font-black text-primary-container">{{ $cancelledReservations }}</h3>
                <span class="rounded bg-rose-100 px-2 py-1 text-xs font-bold text-rose-700">Annulees</span>
            </div>
        </div>
    </div>

    <section class="rounded-2xl bg-surface-container-lowest p-8 shadow-[0_8px_30px_rgba(13,33,55,0.02)]">
        <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
            <div>
                <span class="text-sm font-bold uppercase tracking-[0.2em] text-secondary">Mon Agence</span>
                <h1 class="mt-3 text-3xl font-black tracking-tight text-primary-container">Reservations</h1>
                <p class="mt-2 text-on-surface-variant">{{ $agency->name }}</p>
            </div>
            <div class="rounded-xl bg-surface-container-low px-4 py-3 text-sm font-semibold text-on-surface-variant">
                {{ $reservations->total() }} reservation(s) trouvee(s)
            </div>
        </div>

        <form method="GET" action="{{ route('agency.reservations.index', $agency->slug) }}"
            class="mb-8 rounded-xl bg-surface-container-low p-4 shadow-[0_20px_40px_rgba(13,33,55,0.04)]">
            <div class="grid grid-cols-1 gap-3 md:grid-cols-2 xl:grid-cols-5">
                <div class="xl:col-span-2">
                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-outline-variant">Recherche</label>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Reference, client, email, CIN ou vehicule"
                        class="w-full rounded-lg border-none bg-surface-container-high px-4 py-3 text-sm font-semibold text-on-surface outline-none focus:ring-0">
                </div>
                <div>
                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-outline-variant">Statut</label>
                    <select name="status"
                        class="w-full rounded-lg border-none bg-surface-container-high px-4 py-3 text-sm font-semibold text-on-surface outline-none focus:ring-0">
                        <option value="">Tous</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
                <div>
                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-outline-variant">Date debut</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}"
                        class="w-full rounded-lg border-none bg-surface-container-high px-4 py-3 text-sm font-semibold text-on-surface outline-none focus:ring-0">
                </div>
                <div>
                    <label class="mb-2 block text-[10px] font-bold uppercase tracking-widest text-outline-variant">Date fin</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}"
                        class="w-full rounded-lg border-none bg-surface-container-high px-4 py-3 text-sm font-semibold text-on-surface outline-none focus:ring-0">
                </div>
            </div>

            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-sm text-on-surface-variant">Gerez les demandes de reservation de vos clients depuis cette page.</p>
                <div class="flex gap-3">
                    <a href="{{ route('agency.reservations.index', $agency->slug) }}"
                        class="rounded-lg px-4 py-3 text-sm font-bold text-on-surface-variant transition-colors hover:bg-surface-container-high hover:text-on-surface">
                        Reinitialiser
                    </a>
                    <button type="submit"
                        class="gold-gradient rounded-lg px-6 py-3 text-sm font-black uppercase tracking-wider text-white transition-opacity hover:opacity-90">
                        Filtrer
                    </button>
                </div>
            </div>
        </form>

        @if ($reservations->isEmpty())
            <div class="py-24 text-center">
                <span class="material-symbols-outlined mb-4 block text-6xl text-on-surface-variant">calendar_today</span>
                <p class="mb-2 text-xl font-semibold text-on-surface-variant">Aucune reservation pour les filtres actuels.</p>
                <p class="text-sm text-on-surface-variant">Les nouvelles demandes apparaitront ici des qu'un client reserve un vehicule.</p>
            </div>
        @else
            <div class="overflow-hidden rounded-xl border border-surface-container">
                <div class="grid grid-cols-12 gap-4 bg-primary-container px-6 py-4 text-xs font-bold uppercase tracking-widest text-white">
                    <div class="col-span-2">Reference / Client</div>
                    <div class="col-span-2">Vehicule</div>
                    <div class="col-span-2">Periode</div>
                    <div class="col-span-1">Duree</div>
                    <div class="col-span-1">Montant</div>
                    <div class="col-span-2">Statut</div>
                    <div class="col-span-2 text-right">Actions</div>
                </div>

                @foreach ($reservations as $reservation)
                    <div class="grid grid-cols-12 items-center gap-4 border-b border-surface-container px-6 py-4 transition-colors hover:bg-surface-container-low">
                        <div class="col-span-2">
                            <p class="font-bold text-on-surface">{{ $reservation->reference }}</p>
                            <p class="mt-1 text-sm font-semibold text-primary-container">
                                {{ $reservation->client?->full_name ?? 'Client inconnu' }}
                            </p>
                            <p class="text-xs text-on-surface-variant">{{ $reservation->client?->email }}</p>
                        </div>

                        <div class="col-span-2">
                            <p class="font-semibold text-on-surface">
                                {{ $reservation->vehicle?->brand }} {{ $reservation->vehicle?->model }}
                            </p>
                            <p class="text-xs text-on-surface-variant">{{ $reservation->vehicle?->registration_number }}</p>
                        </div>

                        <div class="col-span-2">
                            <p class="font-medium text-on-surface">
                                {{ $reservation->start_date->format('d/m/Y') }} - {{ $reservation->end_date->format('d/m/Y') }}
                            </p>
                        </div>

                        <div class="col-span-1 text-sm font-semibold text-on-surface">
                            {{ $reservation->duration_days }} j
                        </div>

                        <div class="col-span-1 text-sm font-black text-secondary">
                            {{ number_format($reservation->total_price, 0, ',', ' ') }} DH
                        </div>

                        <div class="col-span-2">
                            @php
                                $statusClasses = match ($reservation->status) {
                                    'confirmed' => 'bg-emerald-100 text-emerald-700',
                                    'pending' => 'bg-amber-100 text-amber-700',
                                    'completed' => 'bg-sky-100 text-sky-700',
                                    'cancelled' => 'bg-rose-100 text-rose-700',
                                    default => 'bg-surface-container text-on-surface-variant',
                                };
                            @endphp
                            <span class="rounded px-2 py-1 text-[10px] font-bold uppercase tracking-widest {{ $statusClasses }}">
                                {{ $reservation->status_label }}
                            </span>
                        </div>

                        <div class="col-span-2 flex justify-end gap-2">
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
                    </div>
                @endforeach
            </div>

            <div class="mt-8">
                {{ $reservations->links() }}
            </div>
        @endif
    </section>
</x-agency-dashboard-layout>
