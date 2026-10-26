@use('Illuminate\Support\Facades\Storage')
<x-app-layout title="Agences">
    <main class="pt-16">
        <!-- Header Banner -->
        <header class="bg-primary-container text-white py-16 px-6">
            <div class="max-w-7xl mx-auto text-center">
                <nav class="flex justify-center mb-6 space-x-2 text-sm text-surface-variant font-medium opacity-80">

                </nav>
                <h1 class="text-4xl md:text-5xl font-extrabold tracking-tight mb-4">Our Partner Agencies</h1>
                <p class="text-lg text-outline-variant max-w-2xl mx-auto mb-10">Discover trusted car rental agencies
                    across Morocco with premium service standards.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <div
                        class="bg-primary-fixed-dim/10 border border-white/10 px-6 py-2 rounded-full flex items-center gap-2">
                        <span class="text-secondary-container font-bold">{{ $agencies->total() }}+</span>
                        <span class="text-sm uppercase tracking-wider font-semibold">Agencies</span>
                    </div>
                    <div
                        class="bg-primary-fixed-dim/10 border border-white/10 px-6 py-2 rounded-full flex items-center gap-2">
                        <span class="text-secondary-container font-bold">{{ $nbr_cities }}+</span>
                        <span class="text-sm uppercase tracking-wider font-semibold">Cities</span>
                    </div>
                    <div
                        class="bg-primary-fixed-dim/10 border border-white/10 px-6 py-2 rounded-full flex items-center gap-2">
                        <span class="text-secondary-container font-bold">{{ $total_vehicles }}+</span>
                        <span class="text-sm uppercase tracking-wider font-semibold">Vehicles</span>
                    </div>
                </div>
            </div>
        </header>
        <!-- Search & Filter Bar -->
        <form method="GET" action="{{ route('agency.index') }}">
            <section class="max-w-7xl mx-auto px-6 -mt-8 relative z-10">
                <div
                    class="bg-surface-container-lowest rounded-xl shadow-xl p-4 md:p-2 border border-outline-variant/10">
                    <div class="flex flex-col md:flex-row gap-2">
                        <div class="flex-grow md:w-[40%] flex items-center px-4 bg-surface-container-low rounded-lg">
                            <span class="material-symbols-outlined text-outline">search</span>
                            <input id="search" name="search"
                                class="w-full bg-transparent border-none focus:ring-0 text-on-surface py-3 px-3"
                                placeholder="Search agency name..." type="text" />
                        </div>
                        <div class="md:w-[20%] flex items-center px-4 bg-surface-container-low rounded-lg">
                            <span class="material-symbols-outlined text-outline">location_on</span>
                            <select id="city" name="city"
                                class="w-full bg-transparent border-none focus:ring-0 text-on-surface py-3 px-3 appearance-none">
                                <option value="">All Cities</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city['city'] }}"
                                        {{ old('city') == $city['city'] ? 'selected' : '' }}>
                                        {{ $city['city'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:w-[20%] flex items-center px-4 bg-surface-container-low rounded-lg">
                            <span class="material-symbols-outlined text-outline">sort</span>
                            <select id="sort" name="sort"
                                class="w-full bg-transparent border-none focus:ring-0 text-on-surface py-3 px-3 appearance-none">
                                <option>Sort by: Popular</option>
                                <option>Newest First</option>
                            </select>
                        </div>
                        <button
                            class="md:flex-grow gold-gradient text-white font-bold py-3 px-8 rounded-lg transition-transform active:scale-95 shadow-lg shadow-secondary/20">
                            Search
                        </button>
                    </div>
                </div>
            </section>
        </form>
        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-6 py-12 flex flex-col md:flex-row gap-8">
            <!-- Sidebar Filters -->
            <aside class="w-full md:w-1/4">
                <form method="GET" action="{{ route('agency.index') }}">
                    <div
                        class="sticky top-24 bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-outline-variant/10">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-xs font-black uppercase tracking-widest text-primary">Filter Agencies</h3>
                            <button
                                class="text-[11px] font-bold text-secondary uppercase tracking-wider hover:underline"><a
                                    href="{{ route('agency.index') }}">Clear All</a></button>
                        </div>
                        <div class="mb-8">
                            <h4 class="text-sm font-bold mb-4 text-on-surface">City Location</h4>
                            <div class="space-y-3">
                                @foreach ($cities as $index => $city)
                                    <label class="flex items-center justify-between group cursor-pointer">
                                        <div class="flex items-center gap-3">
                                            <input name="city" value="{{ $city['city'] }}"
                                                class="rounded text-secondary focus:ring-secondary/20 border-outline-variant"
                                                type="radio" />
                                            <span
                                                class="text-sm text-on-surface-variant group-hover:text-primary transition-colors">
                                                {{ $city['city'] }}
                                            </span>
                                        </div>
                                        <span
                                            class="text-[10px] font-bold bg-surface-container-high px-2 py-0.5 rounded text-outline">{{ $agenciesByCity->firstWhere('city', $city['city']) ? $agenciesByCity->firstWhere('city', $city['city'])->total : 0 }}</span>
                                    </label>

                                    @if ($index === 9)
                                        @break
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div>
                            <div class="flex flex-col gap-2">
                                <button
                                    class="w-full py-2 px-4 rounded-full gold-gradient text-white text-xs font-bold shadow-md">Filter</button>
                            </div>
                        </div>
                    </div>
                </form>
            </aside>
            <!-- Agency Grid -->
            <section class="w-full md:w-3/4">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-sm font-bold text-outline uppercase tracking-wider">Agencies</h2>
                    <p class="text-sm text-on-surface-variant">Showing <span
                            class="font-bold text-primary">{{ $agencies->count() }}</span> of {{ $agencies->total() }}
                        agencies</p>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    @foreach ($agencies as $agency)
                        <div
                            class="group bg-surface-container-lowest rounded-xl overflow-hidden shadow-sm hover:shadow-xl hover:-translate-y-1 hover:border-l-4 border-secondary transition-all duration-300">
                            <div class="h-20 bg-primary-container relative">
                                <img alt="" class="w-full h-full object-cover opacity-30"
                                    data-alt="modern car dealership interior with sleek lighting and premium vehicle silhouettes in the background"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuAR-KeSKWI3HHM0QjNMtTp6KZO8V9wsS-Rlyb7oibuXn5tRWL4r3QQSaafNLad0h-iakGdIFTsKlMjcUMRJEUJNMfPt_IUiViTFsOIEQjN5HUWzW_aj0b1StL-94ogVaFcgJtSoy9DPSxTRZK36aauEwbLtXoxbeLq8O1dxz3Hpd-kWMJey3tsTDkFbmXhrkOOyuaAA8Qw6uEIPx5gDFJK58dMev-Cmt9bgY_32ZLicbnxjuvAiIcEqO5Dj12PV7sW1un-nKO9wnX4" />
                            </div>
                            <div class="px-6 pb-6 pt-10 relative">
                                <div
                                    class="absolute -top-10 left-6 h-20 w-20 bg-white rounded-full p-1 shadow-lg overflow-hidden border-[3px] border-white">
                                    <img alt="" class="w-full h-full object-cover"
                                        data-alt="minimalist corporate logo of a car rental company with clean geometric lines"
                                        src="{{ Storage::url($agency->logo) }}" />
                                </div>
                                <h3 class="text-xl font-extrabold text-primary mb-1">{{ $agency->name }}</h3>
                                <div class="flex items-center gap-1 text-secondary font-semibold text-xs mb-4">
                                    <span class="material-symbols-outlined text-[16px]">location_on</span>
                                    <span class="uppercase tracking-tighter">{{ $agency->city }}</span>
                                </div>
                                <div class="flex gap-4 mb-4 text-xs font-medium text-on-surface-variant">
                                    <div class="flex items-center gap-1">
                                        <span
                                            class="material-symbols-outlined text-[14px] text-outline">directions_car</span>
                                        <span>{{ $agency->vehicles->count() }}</span>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <span class="material-symbols-outlined text-[14px] text-outline">history</span>
                                        <span>Since {{ $agency->created_at->format('Y') }}</span>
                                    </div>
                                </div>
                                <p class="text-sm text-outline leading-relaxed mb-6 line-clamp-2">
                                    {{ $agency->description }}</p>
                                <div class="flex gap-3 bg-surface-container-low p-2 rounded-lg">
                                    <a href="{{ route('agency.vehicles', $agency->slug) }}"
                                        class="flex-[3] bg-primary-container text-white py-2 rounded font-bold text-xs hover:opacity-90 text-center no-underline">
                                        View Store
                                    </a>
                                    <button
                                        class="flex-[2] border border-secondary text-secondary py-2 rounded font-bold text-xs hover:bg-secondary/5 transition-colors">Call
                                        Now</button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                <!-- Pagination -->
                <nav class="mt-12 flex justify-center items-center gap-3">
                    {{ $agencies->links() }}
                </nav>
            </section>
        </div>
    </main>
</x-app-layout>
