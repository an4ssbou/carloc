<x-app-layout title="Accueil">
    <!-- Hero Section -->
    <section class="relative min-h-[921px] pt-20 flex items-center overflow-hidden bg-primary-container">
        <div class="absolute inset-0 z-0">
            <div class="absolute inset-0 bg-primary-container/90"></div>
            <img class="w-full h-full object-cover"
                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAHejq_Ta5Tlh-MLk6rqn0Akya7UgewuN3idXNgqdHVP32XEQeAjYdu7UOd3ic3TRxx-I0WdPdtmrZ0WtGNI1q4bGxyh12lc13l2P0wUmfnzcF26t_oP8AtqRQaF3gcjXnaQNSidZ9xxemjo0_kuKtZdYXo71JFd1dyokKLHaaPM2sfdWlF0ame4nmqw30TeiK_sYbd29mTxtAi4I-TxINwbHdiN8k_o-QlFtN3WXuUoJqX2jh6NWXyJmAgiqUcTVIRrfzhmM1gWE8"
                alt="Hero background" />
        </div>
        <div class="relative z-10 max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="text-white">
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tighter mb-6 leading-tight">
                    Find Your Perfect Car, <br /><span class="text-secondary-container">Anywhere in Morocco</span>
                </h1>
                <p class="text-xl text-on-primary-container mb-10 max-w-lg leading-relaxed">
                    Browse hundreds of vehicles from trusted agencies. Book online in minutes with our digital concierge
                    service.
                </p>
                <!-- Search Bar -->
                <form action="{{ route('vehicles.index') }}" method="GET"
                    class="bg-surface-container-lowest p-2 rounded-xl flex flex-col md:flex-row gap-2 shadow-2xl">
                    <div class="flex-1 grid grid-cols-1 md:grid-cols-3 gap-2 p-2">
                        <div class="flex flex-col px-4 py-2 bg-surface-container-high rounded-lg">
                            <label for="city"
                                class="text-[10px] font-bold text-outline-variant uppercase tracking-widest">Location</label>
                            <select id="city" name="city"
                                class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0">
                                <option value="">Choisir une ville</option>
                                @foreach ($cities as $city)
                                    <option value="{{ $city['city'] }}">{{ $city['city'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="flex flex-col px-4 py-2 bg-surface-container-high rounded-lg">
                            <label for="start_date"
                                class="text-[10px] font-bold text-outline-variant uppercase tracking-widest">Pick-up
                                Date</label>
                            <input id="start_date" name="start_date"
                                class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0"
                                type="date" min="{{ now()->toDateString() }}" />
                        </div>
                        <div class="flex flex-col px-4 py-2 bg-surface-container-high rounded-lg">
                            <label for="end_date"
                                class="text-[10px] font-bold text-outline-variant uppercase tracking-widest">Return
                                Date</label>
                            <input id="end_date" name="end_date"
                                class="bg-transparent border-none p-0 text-on-surface font-semibold focus:ring-0"
                                type="date" min="{{ now()->addDay()->toDateString() }}" />
                        </div>
                    </div>
                    <button type="submit"
                        class="gold-gradient px-8 py-4 rounded-lg font-black text-white uppercase tracking-wider flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
                        <span class="material-symbols-outlined">search</span>
                        Search Now
                    </button>
                </form>
            </div>
            <!-- Hero Image -->
            <div class="hidden lg:block relative">
                <div class="absolute -top-20 -right-20 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
                <img class="relative z-10 w-full transform scale-110 drop-shadow-[0_35px_35px_rgba(0,0,0,0.5)]"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDU2k5iZvcc6GcmV3mIw79N-aui1h05NjbvF6nGTGp0d5p3LhVbwovpcYH9f0pipZSVrOHOnmPSzs8CPL1QAmV4fvsbLITLYTKO1Kw5T_YH_vWq350a_5_uYz4DOuClGw_nsL-3W7vZ39A6XjASvXfY91z-hlEqCOHHdUephd1ie6sDR7w5wMZLItbDp-gzLEwCS1tBYmPheVU-AuCOfW8v20ACD_XH7Ekh3un9KIt46SaM79v2QVPP_3xhUQYd9Znnq3ya6jitvtU"
                    alt="Hero car" />
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="bg-primary-container py-12 border-t border-white/5">
        <div class="max-w-7xl mx-auto px-6">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-3xl font-black text-white">{{ $total_vehicles - 1 }}+</div>
                    <div class="text-on-primary-container text-sm font-medium uppercase tracking-widest">Vehicles</div>
                </div>
                <div>
                    <div class="text-3xl font-black text-white">{{ $nbr_agencies }}+</div>
                    <div class="text-on-primary-container text-sm font-medium uppercase tracking-widest">Agencies</div>
                </div>
                <div>
                    <div class="text-3xl font-black text-white">{{ $nbr_cities }}+</div>
                    <div class="text-on-primary-container text-sm font-medium uppercase tracking-widest">Cities</div>
                </div>
                <div>
                    <div class="text-3xl font-black text-white">24/7</div>
                    <div class="text-on-primary-container text-sm font-medium uppercase tracking-widest">Support</div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section class="py-24 bg-surface-container-low">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-20">
                <span class="label-md text-secondary font-bold tracking-[0.2em] uppercase">The Process</span>
                <h2 class="text-4xl font-extrabold mt-4 text-primary tracking-tight">How CarLoc Works</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-16">
                <div class="relative text-center group">
                    <div
                        class="w-20 h-20 bg-surface-container-lowest rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-sm group-hover:shadow-md transition-shadow">
                        <span class="material-symbols-outlined text-4xl text-secondary">location_on</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Choose Your City</h3>
                    <p class="text-on-surface-variant leading-relaxed">Select your pick-up location and dates to browse
                        available cars in your area.</p>
                </div>
                <div class="relative text-center group">
                    <div
                        class="w-20 h-20 bg-surface-container-lowest rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-sm group-hover:shadow-md transition-shadow">
                        <span class="material-symbols-outlined text-4xl text-secondary">directions_car</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Pick Your Ride</h3>
                    <p class="text-on-surface-variant leading-relaxed">Filter by category, price, or agency. View
                        high-quality photos and real specs.</p>
                </div>
                <div class="relative text-center group">
                    <div
                        class="w-20 h-20 bg-surface-container-lowest rounded-2xl flex items-center justify-center mx-auto mb-8 shadow-sm group-hover:shadow-md transition-shadow">
                        <span class="material-symbols-outlined text-4xl text-secondary">verified_user</span>
                    </div>
                    <h3 class="text-xl font-bold mb-4">Book with Confidence</h3>
                    <p class="text-on-surface-variant leading-relaxed">Confirm your booking instantly. Pay securely and
                        receive digital confirmation.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Vehicles -->
    <section class="py-24 bg-surface">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex flex-col md:flex-row justify-between items-end mb-16 gap-6">
                <div>
                    <span class="label-md text-secondary font-bold tracking-[0.2em] uppercase">Featured</span>
                    <h2 class="text-4xl font-extrabold mt-4 text-primary tracking-tight">Top Rated Vehicles</h2>
                </div>
                <a href="{{ route('vehicles.index') }}"
                    class="text-primary font-bold flex items-center gap-2 hover:gap-4 transition-all group">
                    View All Fleet <span
                        class="material-symbols-outlined group-hover:translate-x-1 transition-transform">arrow_forward</span>
                </a>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($top_vehicles as $top_vehicle)
                    <div
                        class="bg-surface-container-lowest rounded-xl overflow-hidden shadow-[0_20px_40px_rgba(13,33,55,0.06)] group">
                        <div class="relative aspect-[16/10] overflow-hidden">
                            <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                src="{{ $top_vehicle->primary_photo_url }}"
                                alt="{{ $top_vehicle->brand }} {{ $top_vehicle->model }}" />
                            <div
                                class="absolute top-4 left-4 bg-primary-container text-white px-3 py-1 text-[10px] font-bold uppercase tracking-widest rounded">
                                {{ $top_vehicle->agency->name }}</div>
                        </div>
                        <div class="p-8">
                            <h4 class="text-2xl font-bold mb-2">{{ $top_vehicle->brand . ' ' . $top_vehicle->model }}
                            </h4>
                            <div class="flex gap-4 text-on-surface-variant text-sm mb-6">
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined text-lg">airline_seat_recline_normal</span>
                                    {{ $top_vehicle->seats }}
                                    Seats</span>
                                <span class="flex items-center gap-1"><span
                                        class="material-symbols-outlined text-lg">settings</span>
                                    {{ $top_vehicle->transmission }}</span>
                            </div>
                            <div class="flex justify-between items-center pt-6 border-t border-surface-container">
                                <div><span class="text-secondary text-2xl font-black">{{ $top_vehicle->price_per_day }}
                                        DH</span><span class="text-on-surface-variant text-xs">/day</span></div>
                                <a href="{{ route('vehicles.show', $top_vehicle) }}"
                                    class="bg-primary-container text-white px-5 py-2.5 rounded font-bold text-sm hover:bg-primary transition-colors inline-flex items-center">
                                    Book Now
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Agencies -->
    <section class="py-24 bg-surface-container-low">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <span class="label-md text-secondary font-bold tracking-[0.2em] uppercase">Trusted Partners</span>
                <h2 class="text-4xl font-extrabold mt-4 text-primary tracking-tight">Our Partner Agencies</h2>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                @foreach ($top_agencies as $top_agency)
                    <div
                        class="bg-surface-container-lowest p-8 rounded-xl flex items-center gap-6 shadow-sm border border-transparent hover:border-secondary/20 transition-all">
                        <div class="w-20 h-20 bg-surface-container-low rounded-lg flex items-center justify-center p-4">
                            <img class="max-w-full opacity-60 grayscale hover:grayscale-0 hover:opacity-100 transition-all"
                                src="{{ Storage::url($top_agency->logo) }}" alt="{{ $top_agency->name }}" />
                        </div>
                        <div>
                            <h4 class="font-bold text-lg">{{ $top_agency->name }}</h4>
                            <p class="text-on-surface-variant text-sm">{{ $top_agency->vehicles->count() }}+ Cars •
                                {{ $top_agency->reservations->count() }} Locations</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-24 bg-primary-container overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div>
                <span class="label-md text-secondary-container font-bold tracking-[0.2em] uppercase">Advantages</span>
                <h2 class="text-4xl md:text-5xl font-extrabold mt-4 text-white tracking-tight leading-tight">Why Choose
                    CarLoc for Your Journey?</h2>
                <p class="text-on-primary-container mt-8 text-lg leading-relaxed">We bridge the gap between premium
                    Moroccan agencies and travelers, ensuring every rental is as smooth as your drive.</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-8">
                <div class="bg-white/5 p-8 rounded-xl border border-white/10">
                    <span class="material-symbols-outlined text-secondary-container text-4xl mb-4">payments</span>
                    <h4 class="text-white font-bold text-lg mb-2">Best Price Guarantee</h4>
                    <p class="text-on-primary-container text-sm">We compare prices across multiple agencies to get you
                        the most value.</p>
                </div>
                <div class="bg-white/5 p-8 rounded-xl border border-white/10">
                    <span class="material-symbols-outlined text-secondary-container text-4xl mb-4">support_agent</span>
                    <h4 class="text-white font-bold text-lg mb-2">24/7 Roadside Help</h4>
                    <p class="text-on-primary-container text-sm">Always available support for any issues during your
                        rental period.</p>
                </div>
                <div class="bg-white/5 p-8 rounded-xl border border-white/10">
                    <span class="material-symbols-outlined text-secondary-container text-4xl mb-4">security</span>
                    <h4 class="text-white font-bold text-lg mb-2">Fully Insured</h4>
                    <p class="text-on-primary-container text-sm">Comprehensive insurance packages from reputable
                        Moroccan providers.</p>
                </div>
                <div class="bg-white/5 p-8 rounded-xl border border-white/10">
                    <span class="material-symbols-outlined text-secondary-container text-4xl mb-4">distance</span>
                    <h4 class="text-white font-bold text-lg mb-2">Unlimited Mileage</h4>
                    <p class="text-on-primary-container text-sm">Travel anywhere across Morocco without worrying about
                        distance caps.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-surface text-center">
        <div class="max-w-3xl mx-auto px-6">
            <h2 class="text-4xl font-extrabold text-primary mb-6">Are you an agency owner?</h2>
            <p class="text-on-surface-variant text-lg mb-12">Join Morocco's largest car rental marketplace and start
                growing your business today. Digitized bookings, more exposure, and professional management tools.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('agency.create') }}"
                    class="gold-gradient text-white px-10 py-4 rounded-lg font-black uppercase tracking-widest shadow-xl hover:shadow-2xl transition-all">Create
                    My Agency</a>
                <a href="/"
                    class="border-2 border-primary-container text-primary-container px-10 py-4 rounded-lg font-black uppercase tracking-widest hover:bg-primary-container hover:text-white transition-all">Learn
                    More</a>
            </div>
        </div>
    </section>

    <!-- Footer -->

</x-app-layout>
