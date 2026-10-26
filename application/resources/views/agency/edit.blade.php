<x-app-layout :title="'Modifier ' . $agency->name">
    <!-- Page Header Banner -->
    <section class="bg-primary-container pt-16 pb-20 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <nav
                class="flex justify-center items-center space-x-2 text-on-primary-container text-sm mb-6 font-label tracking-wide">
                <span></span>
                <span class="material-symbols-outlined text-xs"></span>
                <span class="text-white/60"></span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight">Modify Your Agency</h1>
            <p class="text-on-primary-container max-w-2xl mx-auto text-lg">
                Fill in your information below. Your store will be reviewed and activated within 24 hours.
            </p>
            <div class="w-24 h-1 bg-secondary-container mx-auto mt-8"></div>
        </div>
        <!-- Decorative Elements -->
        <div
            class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-secondary/10 rounded-full blur-3xl">
        </div>
    </section>

    <!-- Form Layout Section -->
    <section class="max-w-7xl mx-auto px-6 -mt-12 mb-24">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-8 items-start">

            <!-- Left Column: The Form -->
            <div class="bg-surface-container-lowest rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] p-8 md:p-12">
                <form method="POST" action="{{ route('agency.update', $agency->slug) }}" class="space-y-12"
                    enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <!-- Section 1: Agency Information -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary-container"
                                    data-icon="corporate_fare">corporate_fare</span>
                            </div>
                            <h2 class="text-2xl font-bold text-primary-container">Agency Information</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <!-- Agency Name -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider"
                                    for="name">Agency Name</label>
                                <input id="name" name="name" value="{{ old('name', $agency->name) }}"
                                    class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('name') ring-2 ring-error @enderror"
                                    type="text" placeholder="Atlas Premium Cars" />
                                @error('name')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- City Dropdown -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider"
                                    for="city">City</label>
                                <select id="city" name="city"
                                    class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none appearance-none">
                                    @foreach ($cities as $city)
                                        <option value="{{ $city['city'] }}"
                                            {{ old('city', $agency->city) == $city['city'] ? 'selected' : '' }}>
                                            {{ $city['city'] }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('city')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Full Address -->
                            <div class="flex flex-col gap-2 md:col-span-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider"
                                    for="address">Full Address</label>
                                <input id="address" name="address" value="{{ old('address', $agency->address) }}"
                                    class="w-full bg-surface-container-high rounded-lg px-4 py-3 focus:bg-white transition-all outline-none border-none @error('address') ring-2 ring-error @enderror"
                                    placeholder="e.g., 45 Boulevard Mohamed V" type="text" />
                                @error('address')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Phone Number -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider"
                                    for="phone">Phone Number</label>
                                <input id="phone" name="phone" value="{{ old('phone', $agency->phone) }}"
                                    class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('phone') ring-2 ring-error @enderror"
                                    placeholder="0612345678" type="tel" />
                                @error('phone')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Professional Email -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider"
                                    for="email">Professional Email</label>
                                <input id="email" name="email" value="{{ old('email', $agency->email) }}"
                                    class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('email') ring-2 ring-error @enderror"
                                    placeholder="contact@agency.ma" type="email" />
                                @error('email')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Section 2: Agency Identity -->
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary-container"
                                    data-icon="star">star</span>
                            </div>
                            <h2 class="text-2xl font-bold text-primary-container">Agency Identity</h2>
                        </div>
                        <div class="space-y-6">

                            <!-- Agency Logo Upload -->
                            <div class="flex flex-col gap-2" x-data="{
                                dragging: false,
                                preview: '{{ $agency->logo ? asset('storage/' . $agency->logo) : null }}',
                                clientError: null,
                                handleChange(event) {
                                    this.processFile(event.target.files[0]);
                                },
                                handleDrop(event) {
                                    this.dragging = false;
                                    this.processFile(event.dataTransfer.files[0]);
                                },
                                processFile(file) {
                                    this.clientError = null;
                                    if (!file) return;
                                    const allowed = ['image/png', 'image/jpeg', 'image/jpg'];
                                    if (!allowed.includes(file.type)) {
                                        this.clientError = 'Unsupported format. Please use PNG or JPG.';
                                        return;
                                    }
                                    if (file.size > 2 * 1024 * 1024) {
                                        this.clientError = 'File too large (max. 2MB).';
                                        return;
                                    }
                                    const reader = new FileReader();
                                    reader.onload = (e) => this.preview = e.target.result;
                                    reader.readAsDataURL(file);
                            
                                    const dt = new DataTransfer();
                                    dt.items.add(file);
                                    this.$refs.logoInput.files = dt.files;
                                }
                            }">
                                <label
                                    class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">Agency
                                    Logo</label>

                                {{-- Input file caché (soumis avec le formulaire) --}}
                                <input type="file" name="logo" id="logo"
                                    accept="image/png,image/jpg,image/jpeg" class="hidden" x-ref="logoInput"
                                    value="{{ old('logo') }}" @change="handleChange($event)">

                                {{-- Zone cliquable / drag & drop --}}
                                <div class="border-2 border-dashed rounded-xl p-8 flex flex-col items-center justify-center text-center bg-surface transition-all cursor-pointer group min-h-[140px]"
                                    :class="dragging
                                        ?
                                        'border-secondary bg-surface-container-low scale-[1.01]' :
                                        'border-outline-variant/30 hover:bg-surface-container-low hover:border-outline-variant/60'"
                                    @click="$refs.logoInput.click()" @dragover.prevent="dragging = true"
                                    @dragleave.prevent="dragging = false" @drop.prevent="handleDrop($event)">
                                    {{-- Aperçu image --}}
                                    <template x-if="preview">
                                        <div class="flex flex-col items-center gap-3">
                                            <img :src="preview" alt="Logo preview"
                                                class="max-h-24 max-w-[200px] rounded-lg object-contain shadow-md ring-2 ring-outline-variant/30">
                                            <p class="text-xs text-secondary font-semibold">Click to change logo</p>
                                        </div>
                                    </template>

                                    {{-- État par défaut --}}
                                    <template x-if="!preview">
                                        <div class="flex flex-col items-center pointer-events-none">
                                            <span
                                                class="material-symbols-outlined text-4xl text-outline mb-4 group-hover:text-secondary transition-colors"
                                                data-icon="cloud_upload">cloud_upload</span>
                                            <p class="text-sm text-on-surface-variant mb-1 font-semibold">Drag and drop
                                                your logo here</p>
                                            <p class="text-xs text-outline">Supports PNG, JPG, JPEG &mdash; Max. 2MB
                                            </p>
                                        </div>
                                    </template>
                                </div>

                                {{-- Erreur côté client (Alpine) --}}
                                <p x-show="clientError" x-text="clientError"
                                    class="text-xs text-error font-medium mt-1"></p>

                                {{-- Erreur côté serveur (Laravel) --}}
                                @error('logo')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Agency Description -->
                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider"
                                    for="description">Agency Description</label>
                                <textarea id="description" name="description"
                                    class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none resize-none @error('description') ring-2 ring-error @enderror"
                                    placeholder="Tell us about your fleet and services..." rows="4">{{ old('description', $agency->description) }}</textarea>
                                @error('description')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-8">
                                <div class="space-y-4">
                                    <div class="bg-error/5 p-8 rounded-xl border border-error/20 mb-12">
                                        <h2 class="text-lg font-bold text-error mb-2 flex items-center gap-2">
                                            <span class="material-symbols-outlined">report_problem</span>
                                            Danger Zone
                                        </h2>
                                        <p class="text-sm text-on-surface-variant mb-6">
                                            Permanently delete your agency and all associated data. This action cannot
                                            be undone.
                                        </p>
                                        <x-danger-button x-data=""
                                            x-on:click.prevent="$dispatch('open-modal', 'confirm-agency-deletion')"
                                            class="border border-error text-error px-6 py-2 rounded-lg text-sm font-bold hover:bg-error hover:text-white transition-all">
                                            Delete My Agency
                                        </x-danger-button>

                                        <x-modal name="confirm-agency-deletion" :show="$errors->agencyDeletion->isNotEmpty()" focusable>
                                            <form method="post" action="{{ route('agency.destroy',auth()->user()->agency->slug) }}"
                                                class="p-6">
                                                @csrf
                                                @method('delete')

                                                <div class="flex items-start gap-4 mb-4 p-4">
                                                    <div
                                                        class="w-12 h-12 rounded-full bg-error/10 flex items-center justify-center flex-shrink-0">
                                                        <span
                                                            class="material-symbols-outlined text-error">report_problem</span>
                                                    </div>
                                                    <div>
                                                        <h2 class="text-lg font-bold text-gray-900">
                                                            Delete your agency?
                                                        </h2>
                                                        <p class="mt-1 text-sm text-gray-600">
                                                            Once your agency is deleted, all of its resources and data
                                                            will be permanently
                                                            deleted. Please enter your password to confirm.
                                                        </p>
                                                    </div>
                                                </div>

                                                <div class="mt-6 p-4">
                                                    <x-input-label for="delete_password" value="{{ __('Password') }}"
                                                        class="sr-only" />
                                                    <x-text-input id="delete_password" name="password"
                                                        type="password" class="mt-1 block w-full"
                                                        placeholder="{{ __('Enter your password') }}" />
                                                    <x-input-error :messages="$errors->agencyDeletion->get('password')" class="mt-2" />
                                                </div>

                                                <div class="mt-6 flex justify-end gap-3 p-4">
                                                    <x-secondary-button x-on:click="$dispatch('close')">
                                                        Cancel
                                                    </x-secondary-button>
                                                    <x-danger-button>
                                                        Yes, Delete My Agency
                                                    </x-danger-button>
                                                </div>
                                            </form>
                                        </x-modal>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <!-- Submit Button -->
                    <button
                        class="w-full gold-gradient text-primary-container py-5 rounded-xl font-black text-lg uppercase tracking-widest flex items-center justify-center gap-3 shadow-xl hover:brightness-110 active:scale-[0.98] transition-all"
                        type="submit">
                        Submit My Agency for Review
                        <span class="material-symbols-outlined" data-icon="send">send</span>
                    </button>

                </form>
            </div>

            <!-- Right Column: Sticky Info Panel -->
            <aside class="lg:sticky lg:top-28 space-y-6">
                <div class="bg-primary-container text-white rounded-xl p-8 overflow-hidden relative">
                    <h3 class="text-xl font-bold mb-8">What happens next?</h3>
                    <!-- Stepper -->
                    <div class="space-y-10 relative">
                        <div class="absolute left-4 top-2 bottom-2 border-l-2 border-dashed border-white/20"></div>
                        <div class="relative flex items-start gap-6">
                            <div
                                class="w-8 h-8 rounded-full bg-secondary-container text-primary-container flex items-center justify-center font-black shrink-0 relative z-10">
                                1</div>
                            <div>
                                <p class="font-bold">Submission</p>
                                <p class="text-sm text-on-primary-container">Your application is sent to our
                                    verification team.</p>
                            </div>
                        </div>
                        <div class="relative flex items-start gap-6">
                            <div
                                class="w-8 h-8 rounded-full bg-white/10 text-white flex items-center justify-center font-black shrink-0 relative z-10 border border-white/20">
                                2</div>
                            <div>
                                <p class="font-bold">Manual Review</p>
                                <p class="text-sm text-on-primary-container">Our agents verify your business documents
                                    and address.</p>
                            </div>
                        </div>
                        <div class="relative flex items-start gap-6">
                            <div
                                class="w-8 h-8 rounded-full bg-white/10 text-white flex items-center justify-center font-black shrink-0 relative z-10 border border-white/20">
                                3</div>
                            <div>
                                <p class="font-bold">Activation</p>
                                <p class="text-sm text-on-primary-container">Once approved, you can start listing your
                                    fleet.</p>
                            </div>
                        </div>
                    </div>
                    <!-- Requirements Checklist -->
                    <div class="mt-12 pt-8 border-t border-white/10 space-y-4">
                        <p class="text-xs font-bold uppercase tracking-widest text-on-primary-container mb-4">
                            Requirements</p>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-sm">
                                <span class="material-symbols-outlined text-secondary-container text-lg"
                                    data-icon="check_circle">check_circle</span>
                                Valid professional email
                            </li>
                            <li class="flex items-center gap-3 text-sm">
                                <span class="material-symbols-outlined text-secondary-container text-lg"
                                    data-icon="check_circle">check_circle</span>
                                Moroccan phone (+212)
                            </li>
                            <li class="flex items-center gap-3 text-sm">
                                <span class="material-symbols-outlined text-secondary-container text-lg"
                                    data-icon="check_circle">check_circle</span>
                                Agency Logo (High-Res)
                            </li>
                            <li class="flex items-center gap-3 text-sm">
                                <span class="material-symbols-outlined text-secondary-container text-lg"
                                    data-icon="check_circle">check_circle</span>
                                Accurate Physical Address
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Warning Box -->
                <div class="bg-[#1A3C5E] rounded-xl p-6 flex gap-4 border border-white/5">
                    <span class="material-symbols-outlined text-secondary-container shrink-0" data-icon="warning"
                        style="font-variation-settings: 'FILL' 1;">warning</span>
                    <p class="text-sm text-white/80 leading-relaxed">
                        <span class="font-bold text-white block mb-1">Important Notice</span>
                        Incomplete or false information will result in a permanent ban from the marketplace platform.
                    </p>
                </div>

                <!-- Illustration -->
                <div class="relative h-48 rounded-xl overflow-hidden group">
                    <img alt="Luxury car showroom"
                        class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuCu_c9mpzTZombA2m4ZM8efDWnYCei3o78aqVMCify15l-g-p12n10nflV66EaZ3Rythe_360XnEj3cTglNKjRLnR0FVHOt1xWWcZgPbt7uT-weXeoQts2F3xLstm0UY5AA5uo35TEW3I3grmxT3I3FWOqBJhZdeUQjqYbSRvItmtEulpeCoq3d7e5-hixt-9AS82ED7v9kmMPL1rk6c10fktUi7gvoY1qVer_kg09TEresX5NfjwvF2bo-jGNK6Jetd2LT8Axykh0" />
                    <div class="absolute inset-0 bg-primary-container/40"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <p class="text-white font-bold text-sm">Partner with the best</p>
                        <p class="text-white/60 text-xs">Join 500+ Moroccan agencies today.</p>
                    </div>
                </div>
            </aside>
        </div>

    </section>
</x-app-layout>
