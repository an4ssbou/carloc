<x-admin-layout :pending-agencies-count="$pendingAgenciesCount" title="Users Management">
    <main class="ml-64 min-h-screen bg-surface">
        <header
            class="sticky top-0 z-40 flex h-16 w-full items-center justify-between bg-white/80 px-8 font-['Inter'] text-sm font-medium text-[#0D2137] backdrop-blur-md">
            <div class="flex flex-col">
                <nav class="mb-0.5 flex items-center space-x-2 text-xs text-slate-500">
                    <span>Dashboard</span>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="font-bold text-[#815500]">Users</span>
                </nav>
                <h2 class="text-lg font-extrabold tracking-tight">Users Management</h2>
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
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Total Users</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $totalUsers }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-primary-container/5 text-primary-container">
                        <span class="material-symbols-outlined text-2xl">group</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Active</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $activeUsersCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-green-100 text-green-700">
                        <span class="material-symbols-outlined text-2xl">verified_user</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Verified</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $verifiedUsersCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-tertiary-fixed-dim/10 text-on-tertiary-container">
                        <span class="material-symbols-outlined text-2xl">mark_email_read</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Agency Owners</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $agencyOwnersCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-secondary-fixed/30 text-secondary">
                        <span class="material-symbols-outlined text-2xl">business_center</span>
                    </div>
                </div>
                <div class="flex items-center justify-between rounded-xl bg-surface-container-lowest p-6 shadow-[0_4px_20px_rgba(13,33,55,0.02)]">
                    <div>
                        <p class="mb-1 text-xs font-bold uppercase tracking-widest text-slate-500">Clients</p>
                        <h3 class="text-3xl font-black text-primary-container">{{ $clientsCount }}</h3>
                    </div>
                    <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-100 text-blue-700">
                        <span class="material-symbols-outlined text-2xl">person</span>
                    </div>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-3 rounded-xl bg-surface-container-low p-3">
                <span class="px-3 py-2 text-xs font-black uppercase tracking-widest text-slate-500">Filter By Role</span>
                <a href="{{ route('admin.users') }}"
                    class="{{ $currentRoleFilter === 'all' ? 'bg-primary-container text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    All ({{ $totalUsers }})
                </a>
                <a href="{{ route('admin.users', ['role' => 'super_admin']) }}"
                    class="{{ $currentRoleFilter === 'super_admin' ? 'bg-primary-container text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Super Admin
                </a>
                <a href="{{ route('admin.users', ['role' => 'agency_owner']) }}"
                    class="{{ $currentRoleFilter === 'agency_owner' ? 'bg-secondary text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Agency Owners ({{ $agencyOwnersCount }})
                </a>
                <a href="{{ route('admin.users', ['role' => 'client']) }}"
                    class="{{ $currentRoleFilter === 'client' ? 'bg-blue-600 text-white' : 'bg-white text-slate-600 hover:bg-slate-50' }} rounded-lg px-4 py-2 text-xs font-bold transition">
                    Clients ({{ $clientsCount }})
                </a>
            </div>

            <div class="overflow-hidden rounded-xl bg-surface-container-lowest shadow-[0_8px_30px_rgba(13,33,55,0.04)]">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse text-left">
                        <thead>
                            <tr class="border-b border-surface-container-high">
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">User</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Contact</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Role</th>
                                <th class="px-6 py-5 text-center text-[10px] font-black uppercase tracking-widest text-slate-400">Reservations</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Agency</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Verification</th>
                                <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-slate-400">Joined</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-surface-container-low">
                            @forelse ($users as $user)
                                <tr class="hover:bg-surface-container-low/40">
                                    <td class="px-6 py-5">
                                        <div class="flex items-center gap-4">
                                            <img alt="{{ $user->full_name }}" class="h-10 w-10 rounded-full object-cover"
                                                src="{{ $user->profile_photo_url }}" />
                                            <div>
                                                <p class="text-sm font-bold text-primary-container">{{ $user->full_name }}</p>
                                                <p class="text-[11px] text-slate-500">#{{ $user->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-medium text-on-surface">{{ $user->email }}</span>
                                            <span class="text-[11px] text-slate-400">{{ $user->phone ?: 'No phone number' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span class="rounded-full px-3 py-1 text-[11px] font-bold uppercase tracking-tight
                                            {{ $user->role === 'super_admin' ? 'bg-primary-container/10 text-primary-container' : '' }}
                                            {{ $user->role === 'agency_owner' ? 'bg-secondary-fixed/30 text-secondary' : '' }}
                                            {{ $user->role === 'client' ? 'bg-blue-100 text-blue-700' : '' }}
                                            {{ $user->role === 'visitor' ? 'bg-slate-100 text-slate-600' : '' }}">
                                            {{ str_replace('_', ' ', $user->role) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span class="text-sm font-semibold text-primary-container">{{ $user->reservations_count }}</span>
                                    </td>
                                    <td class="px-6 py-5">
                                        @if ($user->agency)
                                            <div class="flex flex-col gap-1">
                                                <span class="text-sm font-medium text-on-surface">{{ $user->agency->name }}</span>
                                                <span class="text-[11px] text-slate-400">{{ $user->agency->city }}</span>
                                            </div>
                                        @else
                                            <span class="text-sm text-slate-400">No agency</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-medium {{ $user->hasVerifiedEmail() ? 'text-green-600' : 'text-amber-600' }}">
                                                {{ $user->hasVerifiedEmail() ? 'Verified email' : 'Email pending' }}
                                            </span>
                                            <span class="text-[11px] {{ $user->is_active ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $user->is_active ? 'Active account' : 'Inactive account' }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col gap-1 text-[11px] text-slate-500">
                                            <span>{{ $user->created_at?->format('M d, Y') }}</span>
                                            <span>{{ $user->created_at?->diffForHumans() }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-8 text-center text-sm text-slate-500">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="rounded-xl bg-surface-container-lowest px-6 py-4 shadow-[0_8px_30px_rgba(13,33,55,0.04)]">
                {{ $users->links() }}
            </div>
        </div>
    </main>
</x-admin-layout>
