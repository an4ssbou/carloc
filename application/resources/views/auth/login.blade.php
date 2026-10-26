<x-app-layout title="Connexion">
    <section class="flex min-h-screen bg-surface md:items-stretch">
        <section
            class="flex-grow md:w-1/2 bg-white flex flex-col items-center justify-center px-6 py-12 md:p-20">
            <div
                class="md:hidden w-full h-[120px] bg-primary-container fixed top-0 left-0 z-50 flex items-center justify-center">
                <h1 class="text-4xl font-black tracking-[-0.02em] text-white">
                    CarLoc<span class="text-secondary-container">.</span>
                </h1>
            </div>

            <div class="flex min-h-full w-full flex-1 items-center justify-center">
                <div class="w-full max-w-[420px] mt-[100px] md:mt-0" x-data="{ showPassword: false }">
                <x-auth-session-status class="mb-6 rounded-lg bg-green-50 px-4 py-3 text-sm text-green-700"
                    :status="session('status')" />

                @if ($errors->any())
                    <div class="mb-6 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        {{ __('We could not sign you in. Please check your details and try again.') }}
                    </div>
                @endif

                <div class="mb-10">
                    <p class="text-[11px] font-bold tracking-[0.1em] text-on-surface-variant uppercase mb-2">
                        Welcome back
                    </p>
                    <h2 class="text-4xl font-bold text-primary mb-2">Sign In to CarLoc</h2>
                    <p class="text-on-surface-variant text-[13px]">
                        Enter your credentials to access your private fleet portal.
                    </p>
                </div>

                <div class="flex flex-col sm:flex-row gap-4 mb-8">
                    <button type="button"
                        class="flex-1 flex items-center justify-center gap-3 py-3 px-4 bg-surface-container-low hover:bg-surface-container rounded-lg transition-colors border border-outline-variant/10">
                        <img class="w-5 h-5"
                            alt="Google logo"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuDkKbgB2kkNYRIIGVfKBpMm9NEGramy8jpyKLYURIl-RvU437uip010CdRxK21ljGPTKU32qVpo1N7O0CHPqzIWFK_Gs7e-Gj5VFZKCKyGNSaBJ4GOOt6x4E0Wzp-rviQCDbbH3UcVWSextN3X6Z1Wn6D5vmJObS9FYXbvre0fCiJE08_F0jTAcMMw1Qsskwh2avq9rKQ9-a_z_XOOTwa7s9M3gdF01zSpY6lJCtv-80bDRQUkPWvff9aY-3gCh9xiY5C9JSIkXC2A" />
                        <span class="text-sm font-semibold text-on-surface">Google</span>
                    </button>

                    <button type="button"
                        class="flex-1 flex items-center justify-center gap-3 py-3 px-4 bg-surface-container-low hover:bg-surface-container rounded-lg transition-colors border border-outline-variant/10">
                        <img class="w-5 h-5"
                            alt="Facebook logo"
                            src="https://lh3.googleusercontent.com/aida-public/AB6AXuAmVfwOcl3eskEi636qo6k5kUl0d9QycjNa88ejOGqmDA-H71K_fr7Zfscr9lJE8lzSHg6XNyCnarySRXygl8hV3eczZS8a6RG6O_TJrB3nwypSscCL4CC72wyyJfWsv1CzimjbfUc-IH-3IGq3oY7k78JZzwoShQCxEYbWS3PgzUWIfWnGoOFnE3RkMgrUDUXh04wiu_xbbUqeGrECX3fLXPc5_gT54JVdZ3vjvhOM_b3zbG_Ztd-XMINwPg4x90RG0dttzMkfxeg" />
                        <span class="text-sm font-semibold text-on-surface">Facebook</span>
                    </button>
                </div>

                <div class="relative flex items-center gap-4 mb-8">
                    <div class="flex-grow h-px bg-outline-variant/30"></div>
                    <span class="text-[10px] uppercase font-bold text-outline tracking-widest bg-white px-2">
                        Or continue with email
                    </span>
                    <div class="flex-grow h-px bg-outline-variant/30"></div>
                </div>

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email"
                            class="block text-sm font-bold text-on-surface mb-2 uppercase tracking-wide">
                            Email Address
                        </label>
                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-secondary">
                                mail
                            </span>
                            <input id="email" name="email" type="email" value="{{ old('email') }}"
                                placeholder="name@company.com" required autofocus autocomplete="username"
                                class="w-full pl-12 pr-4 py-4 bg-surface-container-high border-none rounded-lg focus:ring-2 focus:ring-secondary/40 text-on-surface transition-all placeholder:text-outline/60" />
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2 gap-4">
                            <label for="password"
                                class="block text-sm font-bold text-on-surface uppercase tracking-wide">
                                Password
                            </label>
                            @if (Route::has('password.request'))
                                <a class="text-[12px] font-bold text-secondary underline decoration-2 underline-offset-4 hover:text-secondary-container transition-colors"
                                    href="{{ route('password.request') }}">
                                    Forgot password?
                                </a>
                            @endif
                        </div>

                        <div class="relative">
                            <span
                                class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-secondary">
                                lock
                            </span>
                            <input id="password" name="password" type="password" :type="showPassword ? 'text' : 'password'"
                                placeholder="••••••••" required autocomplete="current-password"
                                class="w-full pl-12 pr-12 py-4 bg-surface-container-high border-none rounded-lg focus:ring-2 focus:ring-secondary/40 text-on-surface transition-all placeholder:text-outline/60" />
                            <button
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-outline hover:text-secondary transition-colors"
                                type="button" @click="showPassword = !showPassword"
                                :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                <span class="material-symbols-outlined" x-text="showPassword ? 'visibility_off' : 'visibility'"></span>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-2 text-sm text-red-600" />
                    </div>

                    <div class="flex items-center">
                        <label for="remember"
                            class="relative flex items-center cursor-pointer group">
                            <input id="remember" name="remember" type="checkbox" class="peer sr-only"
                                @checked(old('remember'))>
                            <div
                                class="w-5 h-5 bg-surface-container-high rounded border-none peer-checked:bg-secondary transition-all flex items-center justify-center">
                                <span class="material-symbols-outlined text-white text-[16px] opacity-0 peer-checked:opacity-100"
                                    style="font-variation-settings: 'wght' 700;">
                                    check
                                </span>
                            </div>
                            <span
                                class="ml-3 text-sm font-medium text-on-surface-variant group-hover:text-on-surface transition-colors">
                                Remember me on this device
                            </span>
                        </label>
                    </div>

                    <button
                        class="w-full py-5 bg-[#E8A020] hover:bg-[#d4911d] text-white rounded-lg font-bold tracking-[0.1em] uppercase flex items-center justify-center gap-3 shadow-lg shadow-secondary/20 transition-all active:scale-[0.98]"
                        type="submit">
                        SIGN IN
                        <span class="material-symbols-outlined">arrow_forward</span>
                    </button>
                </form>

                <div class="mt-10 text-center">
                    <p class="text-on-surface-variant text-[14px]">
                        Don't have an account yet?
                        <a class="ml-1 text-secondary font-bold underline decoration-2 underline-offset-4 hover:text-secondary-container transition-colors"
                            href="{{ route('register') }}">
                            Create a free account
                        </a>
                    </p>
                </div>
            </div>
            </div>

            <footer class="pt-10 text-center text-[11px] text-outline uppercase tracking-widest opacity-60">
                © 2024 CarLoc Private Fleet. All rights reserved.
            </footer>
        </section>
    </section>
</x-app-layout>
