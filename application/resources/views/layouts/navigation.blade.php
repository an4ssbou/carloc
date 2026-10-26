@php
    $notificationPreview = auth()->check()
        ? auth()->user()->notifications()->orderByDesc('created_at')->orderByDesc('id')->limit(5)->get()
        : collect();
    $notificationUnreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
@endphp
<nav class="fixed top-0 z-50 w-full bg-primary-container glass-nav shadow-[0_20px_40px_rgba(13,33,55,0.06)]"
    x-data="{ open: false }">
    <div class="flex justify-between items-center w-full px-6 py-4 max-w-7xl mx-auto">

        {{-- Logo --}}
        <div class="text-2xl font-black tracking-tighter text-white">
            <a href="/">CarLoc</a>
        </div>

        {{-- Desktop Links --}}
        <div class="hidden md:flex items-center space-x-8">
            <a class="{{ request()->segment(1) === 'vehicles' ? 'text-[#E8A020]' : 'text-white/90' }} font-semibold hover:text-[#feb234] transition-all duration-300 ease-out"
                href="{{ route('vehicles.index') }}">
                Vehicles
            </a>
            <a class="{{ request()->segment(1) === 'agencies' ? 'text-[#E8A020]' : 'text-white/90' }} font-medium hover:text-[#feb234] transition-all duration-300 ease-out"
                href="{{ route('agency.index') }}">Agencies</a>
            <a class="{{ request()->segment(1) === 'contact' ? 'text-[#E8A020]' : 'text-white/90' }} font-medium hover:text-[#feb234] transition-all duration-300 ease-out"
                href="{{ route('contact') }}">Contact</a>
        </div>

        {{-- Desktop Auth --}}
        @if (Auth::check())
            <div class="hidden md:flex items-center gap-4 ms-6">
                
                <x-notification-menu :notifications="$notificationPreview" :unread-count="$notificationUnreadCount" />

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center gap-2 px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-200 hover:text-white focus:outline-none transition ease-in-out duration-150">
                            @if (Auth::user()->profile_photo)
                                <img class="size-8 rounded-full object-cover shrink-0 border border-white/20"
                                    src="{{ Storage::url(Auth::user()->profile_photo) }}" alt="Avatar">
                            @endif

                            <span>{{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}</span>
                            <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                    clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>
                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('notifications.index')">
                            {{ __('Notifications') }}
                        </x-dropdown-link>
                        <x-dropdown-link :href="route('reservation.index')">
                            {{ __('My Reservations') }}
                        </x-dropdown-link>
                        @if (auth()->user()->agency && auth()->user()->agency->founder_id === auth()->id())
                            <hr class="border-gray-100 my-1">
                            <x-dropdown-link :href="route('agency.dashboard', auth()->user()->agency->slug)">
                                {{ __('Agency Dashboard') }}
                            </x-dropdown-link>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();" class="text-red-500">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>
        @else
            <div class="hidden md:flex items-center space-x-4">
                <a href="{{ route('login') }}"
                    class="text-white/90 font-medium hover:text-white transition-colors duration-200">Login</a>
                <a href="{{ route('register') }}"
                    class="gold-gradient text-white px-6 py-2.5 rounded-md font-bold text-sm tracking-tight scale-95 active:scale-90 transition-transform">Sign
                    Up</a>
            </div>
        @endif

        {{-- Hamburger Button (mobile only) --}}
        <div class="flex items-center gap-4 md:hidden">
            {{-- Cloche mobile --}}
            @if(Auth::check())
            <x-notification-menu :notifications="$notificationPreview" :unread-count="$notificationUnreadCount" />
            @endif

            <button @click="open = !open"
                class="inline-flex items-center justify-center p-2 rounded-md text-white/70 hover:text-white hover:bg-white/10 focus:outline-none transition duration-150 ease-in-out">
                <svg x-show="!open" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
                <svg x-show="open" class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24" style="display:none;">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

    </div>

    {{-- Mobile Menu --}}
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 -translate-y-2" class="md:hidden border-t border-white/10"
        style="display:none;">
        
        <div class="px-6 pt-4 pb-2 space-y-1">
            <a href="{{ route('vehicles.index') }}" class="block px-4 py-3 rounded-lg text-white/90 font-medium hover:bg-white/10 transition-colors">Vehicles</a>
            <a href="{{ route('agency.index') }}" class="block px-4 py-3 rounded-lg text-white/90 font-medium hover:bg-white/10 transition-colors">Agencies</a>
            <a href="{{ route('contact') }}" class="block px-4 py-3 rounded-lg text-white/90 font-medium hover:bg-white/10 transition-colors">Contact</a>
        </div>

        {{-- Mobile Auth --}}
        <div class="px-6 pt-2 pb-6 border-t border-white/10 mt-2">
            @if (Auth::check())
                <div class="flex items-center gap-3 px-4 py-3 mb-2">
                    <img class="size-10 rounded-full object-cover"
                        src="{{ Storage::url(Auth::user()->profile_photo) }}" alt="Avatar">
                    <div>
                        <div class="text-white font-semibold text-sm">
                            {{ Auth::user()->first_name . ' ' . Auth::user()->last_name }}</div>
                        <div class="text-white/50 text-xs">{{ Auth::user()->email }}</div>
                    </div>
                </div>
                <a href="{{ route('notifications.index') }}" class="block px-4 py-3 rounded-lg text-white/90 font-medium hover:bg-white/10 transition-colors flex justify-between items-center">
                    Notifications
                    @if($notificationUnreadCount > 0)
                        <span class="bg-red-600 text-white text-[10px] px-2 py-0.5 rounded-full">{{ $notificationUnreadCount }}</span>
                    @endif
                </a>
                <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-lg text-white/90 font-medium hover:bg-white/10 transition-colors">Profile</a>
                <a href="{{ route('reservation.index') }}" class="block px-4 py-3 rounded-lg text-white/90 font-medium hover:bg-white/10 transition-colors">My Reservations</a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full text-left px-4 py-3 rounded-lg text-red-400 font-medium hover:bg-white/10 transition-colors">Log Out</button>
                </form>
            @else
                <div class="flex flex-col gap-3 mt-4">
                    <a href="{{ route('login') }}" class="w-full text-center border border-white/30 text-white px-6 py-3 rounded-md font-bold text-sm tracking-tight hover:bg-white/10 transition-colors">Login</a>
                    <a href="{{ route('register') }}" class="w-full text-center gold-gradient text-white px-6 py-3 rounded-md font-bold text-sm tracking-tight">Sign Up</a>
                </div>
            @endif
        </div>
    </div>
</nav>
