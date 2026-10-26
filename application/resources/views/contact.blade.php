<x-app-layout title="Contact">
    <main class="pt-20">
        <!-- Hero Header Section -->
        <section class="relative bg-primary-container py-24 overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <img class="w-full h-full object-cover"
                    data-alt="Luxurious dark textured background with subtle architectural patterns and soft moody lighting for a premium automotive brand"
                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBm5ErLfGRN5WGebFCL1m1JGjWfXmC1TxYa9IC1YuFUCpS1-s4CofPZc3Vvc5r4HGezk-BSRb4Wsr448M3T1uUnJRDm99qD-fPYPXg2od1qyHKfk21ClUnkAkIf6X_qukESHpHC5AFt1Gjtbc-aw2yWD0oUZSsYScfLmlSSpHWz-3UFHa793-107TGbeZL63lVmLQvQ5rPBaUSP0iiBb4sbVNF5UDtsIdd4ozyf0Qo11Fk0wE4a6ZNUMWKLbzhykrWfkmt0gzUSNcc" />
            </div>
            <div class="relative max-w-7xl mx-auto px-8">
                <nav class="flex mb-6 text-on-primary-container text-sm font-medium tracking-wide uppercase">
                </nav>
                <h1 class="text-5xl md:text-7xl font-black text-white tracking-tighter mb-4 leading-none">Get In Touch
                </h1>
                <p class="text-on-primary-container text-lg md:text-xl max-w-2xl mb-8">
                    Experience the pinnacle of Moroccan luxury travel. Our dedicated concierge team is available 24/7 to
                    assist with your bespoke rental requirements.
                </p>
                <div class="flex flex-wrap gap-4">
                    <div
                        class="bg-white/5 backdrop-blur-md px-6 py-3 rounded-full border border-white/10 flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary-container">verified</span>
                        <span class="text-white text-sm font-bold">15+ Locations</span>
                    </div>
                    <div
                        class="bg-white/5 backdrop-blur-md px-6 py-3 rounded-full border border-white/10 flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary-container">headset_mic</span>
                        <span class="text-white text-sm font-bold">24/7 Support</span>
                    </div>
                    <div
                        class="bg-white/5 backdrop-blur-md px-6 py-3 rounded-full border border-white/10 flex items-center gap-3">
                        <span class="material-symbols-outlined text-secondary-container">workspace_premium</span>
                        <span class="text-white text-sm font-bold">Premium Fleet</span>
                    </div>
                </div>
            </div>
        </section>
        <!-- Main Content Area -->
        <section class="max-w-7xl mx-auto px-8 py-20">
            <div class="grid grid-cols-1 lg:grid-cols-10 gap-12 items-start">
                <!-- Left Column: Contact Form (55%) -->
                <div
                    class="lg:col-span-6 bg-surface-container-lowest p-8 md:p-12 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
                    <h2 class="text-3xl font-extrabold text-primary mb-8 tracking-tight">Send a Message</h2>
                    <form method="POST" action="{{ route('contact.send') }}" class="space-y-8">
                        @csrf
                        <div class=" md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-outline">Subject</label>
                                <input
                                    name = "subject"
                                    class="w-full bg-surface-container-high border-none rounded-md px-4 py-3 focus:ring-2 focus:ring-secondary focus:bg-surface-container-lowest transition-all"
                                    placeholder="Subject" type="text" required/>
                            </div>
                        </div>
                        <div class=" md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-outline">Email</label>
                                <input
                                    name = "email"
                                    class="w-full bg-surface-container-high border-none rounded-md px-4 py-3 focus:ring-2 focus:ring-secondary focus:bg-surface-container-lowest transition-all"
                                    placeholder="contact@example.com" value="{{ old('email', auth()->user()?->email ?? '') }}" type="text" required/>
                            </div>
                        </div>
                        <div class="md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-outline">Phone
                                    Number</label>
                                <input
                                    name="phone"
                                    class="w-full bg-surface-container-high border-none rounded-md px-4 py-3 focus:ring-2 focus:ring-secondary focus:bg-surface-container-lowest transition-all"
                                    placeholder="+212 600 000 000" value="{{ old('phone', auth()->user()?->phone ?? '') }}" type="tel" required/>
                            </div>
                        </div>
                        <div class="space-y-2">
                            <div class="flex justify-between">
                                <label class="text-xs font-bold uppercase tracking-widest text-outline">Your
                                    Message</label>
                                <span class="text-xs font-bold text-outline">0 / 500</span>
                            </div>
                            <textarea
                                name="texte"
                                class="w-full bg-surface-container-high border-none rounded-md px-4 py-3 focus:ring-2 focus:ring-secondary focus:bg-surface-container-lowest transition-all resize-none"
                                placeholder="How can our concierge assist you today?" rows="6" required></textarea>
                        </div>
                        <button
                            class="w-full gold-gradient text-white py-4 rounded-md font-bold text-lg tracking-tight hover:shadow-lg hover:shadow-secondary/20 transition-all flex items-center justify-center gap-2">
                            Send Request
                            <span class="material-symbols-outlined">send</span>
                        </button>
                    </form>
                </div>
                <!-- Right Column: Info Panel (45%) -->
                <div class="lg:col-span-4 space-y-8">
                    <!-- Contact Methods Card -->
                    <div class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
                        <h3 class="text-xl font-extrabold text-primary mb-6">Contact Channels</h3>
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="bg-surface-container-low p-3 rounded-lg">
                                    <span class="material-symbols-outlined text-secondary">call</span>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Direct Call
                                    </p>
                                    <p class="font-bold text-primary text-lg">+212 762732730</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="bg-surface-container-low p-3 rounded-lg">
                                    <span class="material-symbols-outlined text-secondary">mail</span>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">Email
                                        Inquiry</p>
                                    <p class="font-bold text-primary text-lg">concierge@carloc.ma</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4">
                                <div class="bg-surface-container-low p-3 rounded-lg">
                                    <span class="material-symbols-outlined text-secondary">chat</span>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-outline uppercase tracking-wider mb-1">WhatsApp
                                        Business</p>
                                    <p class="font-bold text-primary text-lg">+212 762732730</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
</x-app-layout>
