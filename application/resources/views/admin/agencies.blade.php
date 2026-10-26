<x-admin-layout :pending-agencies-count="$pendingAgenciesCount" title="Agencies Management">
    @php
        $rejectAgencyId = old('reject_agency_id');
    @endphp
    <main class="ml-64 min-h-screen bg-surface">
        <header
            class="sticky top-0 z-40 flex h-16 w-full items-center justify-between bg-white/80 px-8 font-['Inter'] text-sm font-medium text-[#0D2137] backdrop-blur-md">
            <div class="flex flex-col">
                <nav class="mb-0.5 flex items-center space-x-2 text-xs text-slate-500">
                    <span>Dashboard</span>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="font-bold text-[#815500]">Agencies</span>
                </nav>
                <h2 class="text-lg font-extrabold tracking-tight">Agencies Management</h2>
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

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-4">
                <div
                    class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Total Agencies</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $totalAgencies }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-container/5 text-primary-container">
                        <span class="material-symbols-outlined text-2xl">business</span>
                    </div>
                </div>
                <div
                    class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Active</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $activeAgenciesCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-tertiary-fixed-dim/10 text-on-tertiary-container">
                        <span class="material-symbols-outlined text-2xl">check_circle</span>
                    </div>
                </div>
                <div
                    class="flex items-center justify-between rounded-xl border-l-4 border-secondary bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Pending</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $pendingAgenciesCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-secondary-fixed/30 text-secondary">
                        <span class="material-symbols-outlined text-2xl">pending</span>
                    </div>
                </div>
                <div
                    class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Rejected</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $rejectedAgenciesCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-error/5 text-error">
                        <span class="material-symbols-outlined text-2xl">block</span>
                    </div>
                </div>
            </div>

            <div
                class="flex flex-col justify-between rounded-r-xl border-l-[6px] border-secondary bg-secondary-fixed/10 p-5 md:flex-row md:items-center">
                <div class="mb-4 flex items-center space-x-4 md:mb-0">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-secondary text-white">
                        <span class="material-symbols-outlined">notification_important</span>
                    </div>
                    <div>
                        <h4 class="font-bold text-on-secondary-container">Approval Needed</h4>
                        <p class="text-sm text-on-secondary-container/80">
                            {{ $pendingAgenciesCount }} agencies are waiting for verification.
                        </p>
                    </div>
                </div>
                <a href="{{ route('admin.dashboard') }}#pending-agency-approvals"
                    class="gold-gradient flex items-center space-x-2 rounded-lg px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-secondary/20 transition-opacity hover:opacity-90">
                    <span>Review Pending ({{ $pendingAgenciesCount }})</span>
                    <span class="material-symbols-outlined text-sm">arrow_forward</span>
                </a>
            </div>

            <div class="flex flex-wrap items-center gap-3 rounded-xl bg-surface-container-low p-3">
                <span class="px-3 py-2 text-xs font-black uppercase tracking-widest text-slate-500">Filter By Status</span>
                <a href="{{ route('admin.agencies') }}"
                    class="{{ $currentStatusFilter === 'all' ? 'bg-primary-container text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    All ({{ $totalAgencies }})
                </a>
                <a href="{{ route('admin.agencies', ['status' => 'active']) }}"
                    class="{{ $currentStatusFilter === 'active' ? 'bg-primary-container text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Active ({{ $activeAgenciesCount }})
                </a>
                <a href="{{ route('admin.agencies', ['status' => 'pending']) }}"
                    class="{{ $currentStatusFilter === 'pending' ? 'bg-secondary text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Pending ({{ $pendingAgenciesCount }})
                </a>
                <a href="{{ route('admin.agencies', ['status' => 'rejected']) }}"
                    class="{{ $currentStatusFilter === 'rejected' ? 'bg-error text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Rejected ({{ $rejectedAgenciesCount }})
                </a>
                <a href="{{ route('admin.agencies', ['status' => 'suspended']) }}"
                    class="{{ $currentStatusFilter === 'suspended' ? 'bg-slate-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Suspended ({{ $suspendedAgenciesCount }})
                </a>
            </div>

            <div class="overflow-hidden rounded-xl bg-surface-container-lowest shadow-[0_8px_30px_rgba(13,33,55,0.04)]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-surface-container-high">
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Agency</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Founder</th>
                                <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest text-slate-400">Fleet</th>
                                <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest text-slate-400">Reservations</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Revenue</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Status</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Review</th>
                                <th class="px-6 py-5 text-right text-[10px] font-black uppercase tracking-widest text-slate-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container-low">
                            @forelse ($agencies as $agency)
                                <tr class="{{ $agency->status === 'pending' ? 'border-l-4 border-secondary bg-secondary-fixed/5' : 'hover:bg-surface-container-low/40' }}">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center space-x-4">
                                            <div
                                                class="flex h-10 w-10 items-center justify-center overflow-hidden rounded-lg bg-surface-container-high">
                                                <img alt="{{ $agency->name }}" class="h-full w-full object-cover"
                                                    src="{{ $agency->logo_url }}" />
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-primary-container">{{ $agency->name }}</p>
                                                <p class="text-[11px] text-slate-500">{{ $agency->slug }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-medium text-on-surface">{{ $agency->founder?->full_name ?? 'Unknown founder' }}</span>
                                            <span class="text-[11px] text-slate-400">{{ $agency->city }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="text-sm font-semibold text-primary-container">{{ $agency->vehicles_count }}</span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="text-sm font-semibold text-primary-container">{{ $agency->reservations_count }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="text-sm font-black text-secondary">{{ number_format((float) ($agency->confirmed_revenue ?? 0), 2, '.', ',') }} MAD</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        @if ($agency->status === 'active')
                                            <div class="flex items-center space-x-2">
                                                <span class="h-2 w-2 rounded-full bg-green-500"></span>
                                                <span class="text-[11px] font-bold uppercase tracking-tight text-green-600">Active</span>
                                            </div>
                                        @elseif ($agency->status === 'pending')
                                            <div class="flex items-center space-x-2">
                                                <span class="h-2 w-2 rounded-full bg-secondary"></span>
                                                <span class="text-[11px] font-bold uppercase tracking-tight text-secondary">Pending</span>
                                            </div>
                                        @elseif ($agency->status === 'rejected')
                                            <div class="flex items-center space-x-2">
                                                <span class="h-2 w-2 rounded-full bg-error"></span>
                                                <span class="text-[11px] font-bold uppercase tracking-tight text-error">Rejected</span>
                                            </div>
                                        @elseif ($agency->status === 'suspended')
                                            <div class="flex items-center space-x-2">
                                                <span class="h-2 w-2 rounded-full bg-slate-500"></span>
                                                <span class="text-[11px] font-bold uppercase tracking-tight text-slate-600">Suspended</span>
                                            </div>
                                        @else
                                            <div class="flex items-center space-x-2">
                                                <span class="h-2 w-2 rounded-full bg-slate-400"></span>
                                                <span class="text-[11px] font-bold uppercase tracking-tight text-slate-500">{{ $agency->status }}</span>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1 text-[11px] text-slate-500">
                                            <span>Created {{ $agency->created_at?->diffForHumans() }}</span>
                                            @if ($agency->rejection_reason)
                                                <span class="max-w-xs truncate text-error" title="{{ $agency->rejection_reason }}">
                                                    Reason: {{ $agency->rejection_reason }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-end gap-2">
                                            @if ($agency->status === 'pending')
                                                <form method="POST" action="{{ route('admin.agencies.approve', $agency) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="rounded-lg bg-green-100 p-2 text-green-700 transition hover:bg-green-200"
                                                        title="Approve {{ $agency->name }}">
                                                        <span class="material-symbols-outlined text-lg">check_circle</span>
                                                    </button>
                                                </form>

                                                <button type="button"
                                                    class="rounded-lg bg-red-100 p-2 text-red-700 transition hover:bg-red-200"
                                                    title="Reject {{ $agency->name }}"
                                                    data-open-modal="reject-agency-{{ $agency->id }}">
                                                    <span class="material-symbols-outlined text-lg">cancel</span>
                                                </button>
                                            @endif

                                            <a href="{{ route('admin.agencies.edit', $agency->slug) }}"
                                                class="rounded-lg bg-slate-100 p-2 text-slate-700 transition hover:bg-slate-200"
                                                title="Edit {{ $agency->name }}">
                                                <span class="material-symbols-outlined text-lg">edit</span>
                                            </a>

                                            @if ($agency->status === 'active')
                                                <form method="POST" action="{{ route('admin.agencies.suspend', $agency) }}">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                        class="rounded-lg bg-amber-100 p-2 text-amber-700 transition hover:bg-amber-200"
                                                        title="Suspend {{ $agency->name }}">
                                                        <span class="material-symbols-outlined text-lg">pause_circle</span>
                                                    </button>
                                                </form>
                                            @endif

                                            <form method="POST" action="{{ route('admin.agencies.destroy', $agency->slug) }}"
                                                onsubmit="return confirm('Supprimer cette agence ? Cette action est irreversible.');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="rounded-lg bg-red-100 p-2 text-red-700 transition hover:bg-red-200"
                                                    title="Delete {{ $agency->name }}">
                                                    <span class="material-symbols-outlined text-lg">delete</span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                            @empty
                                <tr>
                                    <td colspan="8" class="px-6 py-8 text-center text-sm text-slate-500">
                                        No agencies found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div>{{ $agencies->links() }}</div>

            @foreach ($agencies->getCollection()->where('status', 'pending') as $agency)
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
                            <button type="button" class="text-slate-400 hover:text-slate-700" data-close-modal>
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
                                    placeholder="Explique pourquoi la demande est refusee...">{{ (string) ($rejectAgencyId == $agency->id ? old('rejection_reason') : '') }}</textarea>
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
            @endforeach
        </div>
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
