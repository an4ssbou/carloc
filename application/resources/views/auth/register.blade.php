<x-app-layout title="Inscription">
    <main class="min-h-screen flex flex-col md:flex-row bg-surface font-body text-on-surface selection:bg-secondary-fixed selection:text-on-secondary-fixed">
        <section
            class="hidden md:flex w-1/2 bg-primary-container p-16 flex-col justify-between relative overflow-hidden">
            <div
                class="absolute top-[-10%] right-[-10%] w-[500px] h-[500px] bg-primary opacity-20 blur-[120px] rounded-full">
            </div>

            <div class="relative z-10">
                <div class="flex items-center gap-3 mb-20">
                    <div class="w-10 h-10 gold-gradient rounded-lg flex items-center justify-center">
                        <span class="material-symbols-outlined text-primary-container"
                            style="font-variation-settings: 'FILL' 1;">
                            directions_car
                        </span>
                    </div>
                    <span class="text-4xl font-black tracking-[-0.02em] text-surface-bright">CarLoc</span>
                </div>

                <div class="max-w-md">
                    <h1 class="text-white text-5xl font-extrabold leading-[1.1] mb-12 tracking-tight">
                        Experience the <span class="text-secondary-container">Gold Standard</span> of Private Travel.
                    </h1>

                    <div class="space-y-10">
                        <div class="relative flex items-start gap-6">
                            <div
                                class="w-8 h-8 rounded-full bg-secondary-container flex items-center justify-center text-primary-container font-bold z-10 shrink-0">
                                1
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-lg">Create your account</h3>
                                <p class="text-on-primary-container text-sm mt-1">
                                    Access our exclusive fleet in minutes with a seamless onboarding experience.
                                </p>
                            </div>
                        </div>

                        <div class="relative flex items-start gap-6">
                            <div
                                class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white border border-outline-variant/20 font-bold z-10 shrink-0">
                                2
                            </div>
                            <div>
                                <h3 class="text-white/60 font-bold text-lg">Browse vehicles</h3>
                                <p class="text-on-primary-container/40 text-sm mt-1">
                                    Choose from our curated collection of luxury sedans and high-performance SUVs.
                                </p>
                            </div>
                        </div>

                        <div class="relative flex items-start gap-6">
                            <div
                                class="w-8 h-8 rounded-full bg-primary flex items-center justify-center text-white border border-outline-variant/20 font-bold z-10 shrink-0">
                                3
                            </div>
                            <div>
                                <h3 class="text-white/60 font-bold text-lg">Book &amp; drive</h3>
                                <p class="text-on-primary-container/40 text-sm mt-1">
                                    Instant confirmation and premium concierge delivery to your preferred location.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div
                class="relative z-10 flex items-center gap-6 mt-12 bg-primary/30 p-6 rounded-2xl backdrop-blur-sm w-fit border border-outline-variant/10">
                <div class="flex -space-x-3">
                    <img class="w-10 h-10 rounded-full border-2 border-primary-container"
                        alt="Professional client portrait"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAc8zV3Vuei7FTx2NBHLgF_l5RLfXXt1S4l98toKi2wsb6riQp216AKqLKduH3-yILuR7wjluIgJ5XNig5YYeRQ_7fjBhVlsGeuOFwx3TK9gCyil_DA8xRXflAcRdW3i9_yUVMQVvNLINaKL28n9ALqqpcnuXfHegNU5Vex-EO6EFugU2OduuxovVKXpB57SYksCEwk_mM3zk5djuToO4Xv_lcvtCO7Gl5oN3pLCQ32FNXlc0KobuSwSdmtEenDZsbK3a3UPVfuaCA" />
                    <img class="w-10 h-10 rounded-full border-2 border-primary-container"
                        alt="Professional client portrait"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuAM6mHIoOWC_-ZbpbucMBcJLth7nC6hKbUyzMp7meHTCou92LWE6Rf0gMWmQ3ns-gDHntO0b-oMHx5rUaYh8AvRpSyU4Fplr29aYClb3Q0ObaTNO4XjZp3gNc-gu1Zi4ax5DydnuuSl0DapwUmR72B_qrQrPRPKMHoUPEzdmrCIUXIxZDwwXYm-E4B2XODqXOVA1AsYkHXy-dFCJUQyx59oIHMHkAfKd2evGEgrbsiJ90Mwn0oWNqs31pvvW4cDv5K0i6IraRUZZEw" />
                    <img class="w-10 h-10 rounded-full border-2 border-primary-container"
                        alt="Professional client portrait"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuDsjoCyjuZwwQ6TPZ44PvVYFok5Pizh1suAv_XzTZPbPEW9wQX7sJ9HnXPS0ur8V-j3yHl2pnhq1F8Z23vJlUTqVOByV_HxevHEcUi47RJhlKUg9WCpqZ03qfHccX3kdgOfV5p71LmRWusqFqHeDldHWfD9E6ay4ZVoBA-9qRtnbL0jSO_1Y2kZKcKZL2uAIQJFpBg9_mpR_bYX5EUDYrKOZwKsQ9QKaO-PjpZYBBxzNIBy0n0FtL8nM34CkoDUjiO5wOoGDmc33J4" />
                </div>
                <div class="text-white text-sm">
                    <span class="block font-bold">Join 312 users</span>
                    <span class="text-on-primary-container italic text-xs">who upgraded their travel this week.</span>
                </div>
            </div>
        </section>

        <header class="md:hidden bg-primary-container p-6 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 gold-gradient rounded flex items-center justify-center">
                    <span class="material-symbols-outlined text-primary-container text-sm"
                        style="font-variation-settings: 'FILL' 1;">
                        directions_car
                    </span>
                </div>
                <span class="font-black text-lg tracking-tight text-white uppercase">CarLoc</span>
            </div>
            <a href="{{ route('login') }}"
                class="text-secondary-container font-semibold text-xs uppercase tracking-widest">
                Sign In
            </a>
        </header>

        <section class="w-full md:w-1/2 bg-white px-8 py-12 md:p-24 flex flex-col justify-center">
            <div class="max-w-lg mx-auto w-full" x-data="{
                showPassword: false,
                showConfirmPassword: false,
                password: '',
                hasLength() { return this.password.length >= 8 },
                hasUppercase() { return /[A-Z]/.test(this.password) },
                hasNumber() { return /[0-9]/.test(this.password) },
                hasSpecial() { return /[^A-Za-z0-9]/.test(this.password) },
                passwordScore() {
                    return [
                        this.hasLength(),
                        this.hasUppercase(),
                        this.hasNumber(),
                        this.hasSpecial(),
                    ].filter(Boolean).length;
                },
                strengthLabel() {
                    if (this.passwordScore() <= 1) return 'Weak';
                    if (this.passwordScore() === 2) return 'Fair';
                    if (this.passwordScore() === 3) return 'Good';
                    return 'Strong';
                }
            }">
                <div class="mb-10">
                    <span class="text-on-surface-variant text-sm tracking-[0.05em] uppercase block mb-2">
                        New here?
                    </span>
                    <h2 class="text-primary-container text-3xl font-extrabold tracking-tight mb-2">
                        Create your CarLoc account
                    </h2>
                    <p class="text-secondary italic font-medium">It's free and takes less than 2 minutes.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-6 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        Please review the highlighted fields and try again.
                    </div>
                @endif

                <form method="POST" action="{{ route('register') }}" class="space-y-6">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative group">
                            <label for="first_name"
                                class="block text-xs font-bold text-primary-container uppercase tracking-wider mb-2 ml-1">
                                First Name
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-secondary-container">
                                    person
                                </span>
                                <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}"
                                    placeholder="John" required autofocus autocomplete="given-name"
                                    class="w-full pl-12 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-secondary-container/50 focus:bg-white transition-all outline-none text-on-surface" />
                            </div>
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <div class="relative group">
                            <label for="last_name"
                                class="block text-xs font-bold text-primary-container uppercase tracking-wider mb-2 ml-1">
                                Last Name
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-secondary-container">
                                    person
                                </span>
                                <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}"
                                    placeholder="Doe" required autocomplete="family-name"
                                    class="w-full pl-12 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-secondary-container/50 focus:bg-white transition-all outline-none text-on-surface" />
                            </div>
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2 text-sm text-red-600" />
                        </div>
                    </div>

                    <div class="relative group">
                        <label for="email"
                            class="block text-xs font-bold text-primary-container uppercase tracking-wider mb-2 ml-1">
                            Email Address
                        </label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-secondary-container">
                                mail
                            </span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}"
                                placeholder="j.doe@example.com" required autocomplete="username"
                                class="w-full pl-12 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-secondary-container/50 focus:bg-white transition-all outline-none text-on-surface" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div class="relative group">
                        <label for="phone"
                            class="block text-xs font-bold text-primary-container uppercase tracking-wider mb-2 ml-1">
                            Phone Number
                        </label>
                        <div class="relative flex">
                            <div
                                class="absolute left-4 top-1/2 -translate-y-1/2 flex items-center gap-2 pr-3 border-r border-outline-variant/30">
                                <div class="w-5 h-3 bg-[#c1272d] flex items-center justify-center relative">
                                    <div class="text-[6px] text-[#006233]">★</div>
                                </div>
                                <span class="text-xs font-bold text-primary-container">+212</span>
                            </div>
                            <input id="phone" name="phone" type="tel" value="{{ old('phone') }}"
                                placeholder="06XXXXXXXX" required autocomplete="tel"
                                class="w-full pl-24 pr-4 py-3 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-secondary-container/50 focus:bg-white transition-all outline-none text-on-surface" />
                        </div>
                        <x-input-error :messages="$errors->get('phone')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="relative group">
                            <label for="password"
                                class="block text-xs font-bold text-primary-container uppercase tracking-wider mb-2 ml-1">
                                Password
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-secondary-container">
                                    lock
                                </span>
                                <input id="password" name="password" :type="showPassword ? 'text' : 'password'"
                                    x-model="password"
                                    placeholder="••••••••" required autocomplete="new-password"
                                    class="w-full pl-12 pr-12 py-3 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-secondary-container/50 focus:bg-white transition-all outline-none text-on-surface" />
                                <button type="button"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-secondary transition-colors"
                                    @click="showPassword = !showPassword">
                                    <span class="material-symbols-outlined"
                                        x-text="showPassword ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>

                            <div class="mt-2 flex items-center justify-between gap-3">
                                <div class="flex flex-1 gap-1">
                                    <div class="h-1 flex-1 rounded-full transition-colors duration-200"
                                        :class="passwordScore() >= 1 ? 'bg-red-400' : 'bg-surface-container-high'"></div>
                                    <div class="h-1 flex-1 rounded-full transition-colors duration-200"
                                        :class="passwordScore() >= 2 ? 'bg-orange-400' : 'bg-surface-container-high'"></div>
                                    <div class="h-1 flex-1 rounded-full transition-colors duration-200"
                                        :class="passwordScore() >= 3 ? 'bg-yellow-400' : 'bg-surface-container-high'"></div>
                                    <div class="h-1 flex-1 rounded-full transition-colors duration-200"
                                        :class="passwordScore() >= 4 ? 'bg-emerald-500' : 'bg-surface-container-high'"></div>
                                </div>
                                <span class="min-w-[52px] text-[10px] font-bold uppercase tracking-wider transition-colors duration-200"
                                    :class="{
                                        'text-outline': passwordScore() === 0,
                                        'text-red-500': passwordScore() === 1,
                                        'text-orange-500': passwordScore() === 2,
                                        'text-yellow-600': passwordScore() === 3,
                                        'text-emerald-600': passwordScore() === 4,
                                    }"
                                    x-text="password ? strengthLabel() : 'Empty'"></span>
                            </div>

                            <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                        </div>

                        <div class="relative group">
                            <label for="password_confirmation"
                                class="block text-xs font-bold text-primary-container uppercase tracking-wider mb-2 ml-1">
                                Confirm Password
                            </label>
                            <div class="relative">
                                <span
                                    class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-secondary-container">
                                    lock_reset
                                </span>
                                <input id="password_confirmation" name="password_confirmation"
                                    :type="showConfirmPassword ? 'text' : 'password'" placeholder="••••••••" required
                                    autocomplete="new-password"
                                    class="w-full pl-12 pr-12 py-3 bg-surface-container-low border-none rounded-lg focus:ring-2 focus:ring-secondary-container/50 focus:bg-white transition-all outline-none text-on-surface" />
                                <button type="button"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-secondary transition-colors"
                                    @click="showConfirmPassword = !showConfirmPassword">
                                    <span class="material-symbols-outlined"
                                        x-text="showConfirmPassword ? 'visibility_off' : 'visibility'"></span>
                                </button>
                            </div>
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-sm text-red-600" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-y-2 p-4 bg-surface-container-low rounded-xl">
                        <div class="flex items-center gap-2 text-[10px] uppercase font-bold transition-colors duration-200"
                            :class="hasLength() ? 'text-on-tertiary-container' : 'text-outline'">
                            <span class="material-symbols-outlined text-[14px]"
                                :style="hasLength() ? `font-variation-settings: 'FILL' 1;` : ''"
                                x-text="hasLength() ? 'check_circle' : 'circle'"></span>
                            8 Characters
                        </div>
                        <div class="flex items-center gap-2 text-[10px] uppercase font-bold transition-colors duration-200"
                            :class="hasUppercase() ? 'text-on-tertiary-container' : 'text-outline'">
                            <span class="material-symbols-outlined text-[14px]"
                                :style="hasUppercase() ? `font-variation-settings: 'FILL' 1;` : ''"
                                x-text="hasUppercase() ? 'check_circle' : 'circle'"></span>
                            1 Uppercase
                        </div>
                        <div class="flex items-center gap-2 text-[10px] uppercase font-bold transition-colors duration-200"
                            :class="hasNumber() ? 'text-on-tertiary-container' : 'text-outline'">
                            <span class="material-symbols-outlined text-[14px]"
                                :style="hasNumber() ? `font-variation-settings: 'FILL' 1;` : ''"
                                x-text="hasNumber() ? 'check_circle' : 'circle'"></span>
                            1 Number
                        </div>
                        <div class="flex items-center gap-2 text-[10px] uppercase font-bold transition-colors duration-200"
                            :class="hasSpecial() ? 'text-on-tertiary-container' : 'text-outline'">
                            <span class="material-symbols-outlined text-[14px]"
                                :style="hasSpecial() ? `font-variation-settings: 'FILL' 1;` : ''"
                                x-text="hasSpecial() ? 'check_circle' : 'circle'"></span>
                            Special Char
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="pt-1">
                            <input id="terms" type="checkbox"
                                class="w-5 h-5 rounded text-secondary border-outline-variant focus:ring-secondary-container bg-surface-container-low cursor-pointer" />
                        </div>
                        <label class="text-xs text-on-surface-variant leading-relaxed" for="terms">
                            I agree to the <a class="text-secondary font-bold hover:underline" href="#">Terms of
                                Service</a> and <a class="text-secondary font-bold hover:underline" href="#">Privacy
                                Policy</a>. I understand that my data will be used to enhance my booking experience.
                        </label>
                    </div>

                    <button
                        class="w-full gold-gradient py-4 rounded-lg text-primary-container font-black uppercase tracking-[0.1em] text-sm flex items-center justify-center gap-2 shadow-xl shadow-secondary/20 hover:scale-[1.02] active:scale-[0.98] transition-all group"
                        type="submit">
                        <span class="material-symbols-outlined text-xl group-hover:rotate-12 transition-transform">
                            person_add
                        </span>
                        Create My Account
                    </button>

                    <div class="text-center">
                        <p class="text-sm text-on-surface-variant">
                            Already have an account?
                            <a class="text-primary font-bold hover:text-secondary transition-colors underline decoration-secondary/30"
                                href="{{ route('login') }}">
                                Sign In here
                            </a>
                        </p>
                    </div>
                </form>
            </div>
        </section>
    </main>

    <div class="fixed bottom-0 right-0 p-8 pointer-events-none opacity-5 md:opacity-10">
        <span class="material-symbols-outlined text-[120px] text-primary-container">verified_user</span>
    </div>
</x-app-layout>
