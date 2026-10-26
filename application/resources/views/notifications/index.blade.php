<x-app-layout title="Notifications">
    <style>
        .bg-gold-gradient { background: linear-gradient(135deg, #815500 0%, #feb234 100%); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
    </style>

    <main class="min-h-screen pb-24">
        <header class="bg-primary-container px-8 pt-12 pb-24">
            <div class="mx-auto max-w-[860px] text-white">
                <nav class="mb-6 flex items-center text-xs font-semibold uppercase tracking-wide text-on-primary-container">
                    <span>Home</span>
                    <span class="material-symbols-outlined mx-2 text-[14px]">chevron_right</span>
                    <span class="text-outline-variant">Notifications</span>
                </nav>

                <div class="flex flex-col justify-between gap-6 md:flex-row md:items-end">
                    <div>
                        <div class="mb-2 flex items-center gap-3">
                            <span class="material-symbols-outlined text-4xl text-secondary-container">notifications</span>
                            <h1 class="text-4xl font-black tracking-tight">My Notifications</h1>
                        </div>
                        <p class="max-w-md text-lg text-on-primary-container">Restez informé de vos activités et réservations.</p>
                    </div>
                </div>

                <div class="mt-10 flex flex-wrap gap-8 border-t border-white/10 pt-8">
                    <div class="flex items-center gap-3">
                        <span class="text-3xl font-bold">{{ $unreadCount }}</span>
                        <span class="text-xs font-bold uppercase tracking-widest text-on-primary-container">Nouveau</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-3xl font-bold opacity-60">{{ $totalCount }}</span>
                        <span class="text-xs font-bold uppercase tracking-widest text-on-primary-container">Historique Total</span>
                    </div>
                </div>
            </div>
        </header>

        <section class="mx-auto mt-12 max-w-[860px] px-4">
            <div class="space-y-4">
                @forelse($notifications as $notification)
                    @php $isBrandNew = in_array($notification->id, $unreadIds); @endphp

                    <div class="group relative flex flex-col gap-5 rounded-xl border-l-[3px] {{ $isBrandNew ? 'border-secondary bg-[#FFFBF0]' : 'border-transparent bg-white shadow-sm' }} p-5 transition-all hover:translate-x-1 md:flex-row">
                        
                        {{-- Badge Nouveau --}}
                        @if($isBrandNew)
                            <div class="absolute top-4 right-4 flex items-center gap-2">
                                <span class="bg-gold-gradient rounded-full px-3 py-1 text-[8px] font-black uppercase text-white shadow-lg">Nouveau</span>
                            </div>
                        @endif

                        <div class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-full {{ $isBrandNew ? 'bg-amber-50 text-amber-600' : 'bg-slate-50 text-slate-400' }} shadow-inner">
                            <span class="material-symbols-outlined text-3xl">
                                {{ $notification->data['icon'] ?? 'notifications' }}
                            </span>
                        </div>

                        <div class="flex-1">
                            <div class="mb-1 flex items-start justify-between">
                                <h3 class="text-lg font-bold {{ $isBrandNew ? 'text-primary-container' : 'text-slate-600' }}">
                                    {{ $notification->data['title'] }}
                                </h3>
                                <span class="text-[10px] font-bold uppercase text-outline">
                                    {{ $notification->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <p class="text-sm leading-relaxed {{ $isBrandNew ? 'text-on-surface-variant' : 'text-slate-400' }}">
                                {{ $notification->data['message'] }}
                            </p>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-24 text-center">
                        <span class="material-symbols-outlined text-6xl text-outline-variant opacity-20">notifications_off</span>
                        <h2 class="mt-4 text-2xl font-black text-primary-container">Aucune notification</h2>
                        <p class="text-on-surface-variant">Tout est à jour ici !</p>
                    </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($notifications->hasPages())
                <div class="mt-12 flex justify-center">
                    {{ $notifications->links() }}
                </div>
            @endif
        </section>
    </main>
</x-app-layout>
