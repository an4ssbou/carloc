<x-app-layout title="Ajouter un Vehicule">
    <!-- Page Header Banner -->
    <section class="bg-primary-container pt-16 pb-20 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 relative z-10 text-center">
            <nav class="flex justify-center items-center space-x-2 text-on-primary-container text-sm mb-6 font-label tracking-wide">
                <a href="{{ route('vehicles.index') }}" class="hover:text-white transition-colors">Véhicules</a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="text-white/60">Ajouter</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight">Ajouter un Véhicule</h1>
            <p class="text-on-primary-container max-w-2xl mx-auto text-lg">
                Remplissez les informations de votre véhicule. Il sera disponible à la location immédiatement.
            </p>
            <div class="w-24 h-1 bg-secondary-container mx-auto mt-8"></div>
        </div>
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
    </section>

    <!-- Form Layout Section -->
    <section class="max-w-7xl mx-auto px-6 -mt-12 mb-24"
        x-data='{
            activeStep: 1,
            init() {
                const sections = this.$root.querySelectorAll("[data-step-section]");
                const observer = new IntersectionObserver((entries) => {
                    const visibleEntries = entries
                        .filter(entry => entry.isIntersecting)
                        .sort((a, b) => b.intersectionRatio - a.intersectionRatio);

                    if (visibleEntries.length > 0) {
                        this.activeStep = Number(visibleEntries[0].target.dataset.stepSection);
                    }
                }, {
                    threshold: [0.25, 0.5, 0.75],
                    rootMargin: "-15% 0px -45% 0px"
                });

                sections.forEach(section => observer.observe(section));
            }
        }'>
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-8 items-start">

            <!-- Left Column: The Form -->
            <div class="bg-surface-container-lowest rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] p-8 md:p-12">

                {{-- Erreurs --}}
                @if($errors->any())
                    <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('vehicles.store') }}" method="POST" enctype="multipart/form-data" class="space-y-12">
                    @csrf

                    {{-- Agence cachée --}}
                    <input type="hidden" name="agency_id" value="{{ $agency->id }}">

                    <!-- Section 1: Informations générales -->
                    <div class="space-y-8" id="vehicle-step-1" data-step-section="1">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary-container">directions_car</span>
                            </div>
                            <h2 class="text-2xl font-bold text-primary-container">Informations générales</h2>
                        </div>

                        {{-- Agence affichée --}}
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">Agence</label>
                            <div class="w-full bg-surface-container-high rounded-lg px-4 py-3 text-on-surface font-semibold flex items-center gap-2">
                                <span class="material-symbols-outlined text-secondary text-lg">store</span>
                                {{ $agency->name }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="brand">Marque *</label>
                                <input id="brand" name="brand" value="{{ old('brand') }}"
                                       type="text" placeholder="Ex: Mercedes-Benz"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('brand') ring-2 ring-error @enderror"/>
                                @error('brand')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="model">Modèle *</label>
                                <input id="model" name="model" value="{{ old('model') }}"
                                       type="text" placeholder="Ex: C-Class"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('model') ring-2 ring-error @enderror"/>
                                @error('model')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="year">Année *</label>
                                <input id="year" name="year" value="{{ old('year') }}"
                                       type="number" placeholder="Ex: 2026" min="1990" max="{{ now()->year }}"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('year') ring-2 ring-error @enderror"/>
                                @error('year')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="registration_number">Immatriculation *</label>
                                <input id="registration_number" name="registration_number" value="{{ old('registration_number') }}"
                                    type="text" placeholder="Ex: 145784-ط-1"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('registration_number') ring-2 ring-error @enderror"/>
                                @error('registration_number')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="color">Couleur *</label>
                                <input id="color" name="color" value="{{ old('color') }}"
                                       type="text" placeholder="Ex: Noir"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('color') ring-2 ring-error @enderror"/>
                                @error('color')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="mileage">Kilométrage</label>
                                <input id="mileage" name="mileage" value="{{ old('mileage', 0) }}"
                                       type="number" min="0"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none"/>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="seats">Nombre de places *</label>
                                <input id="seats" name="seats" value="{{ old('seats', 5) }}"
                                       type="number" min="1" max="20"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('seats') ring-2 ring-error @enderror"/>
                                @error('seats')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <!-- Section 2: Caractéristiques techniques -->
                    <div class="space-y-8" id="vehicle-step-1-tech" data-step-section="1">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary-container">settings</span>
                            </div>
                            <h2 class="text-2xl font-bold text-primary-container">Caractéristiques techniques</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="condition">État *</label>
                                <select id="condition" name="condition"
                                        class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none appearance-none">
                                    <option value="new" {{ old('condition') == 'new' ? 'selected' : '' }}>Neuf</option>
                                    <option value="good" {{ old('condition', 'good') == 'good' ? 'selected' : '' }}>Bon état</option>
                                    <option value="used" {{ old('condition') == 'used' ? 'selected' : '' }}>Usagé</option>
                                </select>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="fuel_type">Carburant *</label>
                                <select id="fuel_type" name="fuel_type"
                                        class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none appearance-none">
                                    <option value="gasoline" {{ old('fuel_type') == 'gasoline' ? 'selected' : '' }}>Essence</option>
                                    <option value="diesel" {{ old('fuel_type') == 'diesel' ? 'selected' : '' }}>Diesel</option>
                                    <option value="electric" {{ old('fuel_type') == 'electric' ? 'selected' : '' }}>Électrique</option>
                                    <option value="hybrid" {{ old('fuel_type') == 'hybrid' ? 'selected' : '' }}>Hybride</option>
                                </select>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="transmission">Transmission *</label>
                                <select id="transmission" name="transmission"
                                        class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none appearance-none">
                                    <option value="manual" {{ old('transmission') == 'manual' ? 'selected' : '' }}>Manuelle</option>
                                    <option value="automatic" {{ old('transmission') == 'automatic' ? 'selected' : '' }}>Automatique</option>
                                </select>
                            </div>

                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="description">Description</label>
                            <textarea id="description" name="description" rows="4"
                                      placeholder="Décrivez le véhicule, ses équipements..."
                                      class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none resize-none">{{ old('description') }}</textarea>
                        </div>

                        <input type="hidden" name="has_gps" value="0">
                        <label class="flex items-center gap-4 p-4 rounded-lg bg-surface hover:bg-surface-container-low transition-colors cursor-pointer group">
                            <input type="checkbox" name="has_gps" id="has_gps" value="1"
                                   {{ old('has_gps') ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-outline-variant text-secondary focus:ring-secondary"/>
                            <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors font-medium">
                                Ce véhicule est équipé d’un GPS de géolocalisation
                            </span>
                        </label>
                        <p class="text-sm text-on-surface-variant">Le suivi nécessite un émetteur connecté. Cette option seule n’envoie aucune position. Une démonstration simulée est disponible dans le suivi GPS local.</p>

                        <label class="flex items-center gap-4 p-4 rounded-lg bg-surface hover:bg-surface-container-low transition-colors cursor-pointer group">
                            <input type="checkbox" name="is_featured" id="is_featured" value="1"
                                   {{ old('is_featured') ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-outline-variant text-secondary focus:ring-secondary"/>
                            <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors font-medium">
                                Mettre ce véhicule en vedette pour l'agence. Si un autre véhicule est déjà en vedette, il sera remplacé.
                            </span>
                        </label>
                    </div>

                    <!-- Section 3: Tarifs -->
                    <div class="space-y-8" id="vehicle-step-2" data-step-section="2">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary-container">payments</span>
                            </div>
                            <h2 class="text-2xl font-bold text-primary-container">Tarifs (en DH)</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="price_per_day">Prix par jour *</label>
                                <input id="price_per_day" name="price_per_day" value="{{ old('price_per_day') }}"
                                       type="number" min="0" step="0.01" placeholder="Ex: 500 DH"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('price_per_day') ring-2 ring-error @enderror"/>
                                @error('price_per_day')
                                    <p class="text-xs text-error font-medium">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="price_week">Prix par semaine</label>
                                <input id="price_week" name="price_week" value="{{ old('price_week') }}"
                                       type="number" min="0" step="0.01" placeholder="Ex: 3000 DH"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none"/>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="price_two_weeks">Prix pour 2 semaines</label>
                                <input id="price_two_weeks" name="price_two_weeks" value="{{ old('price_two_weeks') }}"
                                       type="number" min="0" step="0.01" placeholder="Ex: 5500 DH"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none"/>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="price_month">Prix par mois</label>
                                <input id="price_month" name="price_month" value="{{ old('price_month') }}"
                                       type="number" min="0" step="0.01" placeholder="Ex: 9000 DH"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none"/>
                            </div>

                        </div>
                    </div>

                    
                    <!-- Section 4: Photos -->
                        <div class="space-y-8"
                            id="vehicle-step-3"
                            data-step-section="3"
                            x-data="{
                                previews: [],
                                handleChange(event) {
                                    this.previews = [];
                                    Array.from(event.target.files).forEach((file, index) => {
                                        const reader = new FileReader();
                                        reader.onload = (e) => {
                                            this.previews.push({ src: e.target.result, primary: index === 0 });
                                        };
                                        reader.readAsDataURL(file);
                                    });
                                }
                            }">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary-container">photo_library</span>
                            </div>
                            <h2 class="text-2xl font-bold text-primary-container">Photos du véhicule</h2>
                        </div>

                        <div class="border-2 border-dashed rounded-xl p-8 flex flex-col items-center justify-center text-center bg-surface transition-all cursor-pointer group min-h-[140px]"
                             :class="previews.length > 0 ? 'border-secondary' : 'border-outline-variant/30 hover:bg-surface-container-low hover:border-outline-variant/60'"
                             @click="$refs.photosInput.click()">

                            <template x-if="previews.length === 0">
                                <div class="flex flex-col items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-4xl text-outline mb-4 group-hover:text-secondary transition-colors">cloud_upload</span>
                                    <p class="text-sm text-on-surface-variant mb-1 font-semibold">Glissez vos photos ici ou cliquez pour sélectionner</p>
                                    <p class="text-xs text-outline">Maximum 10 photos • JPG, PNG, WEBP • Max 2MB chacune</p>
                                </div>
                            </template>

                            <template x-if="previews.length > 0">
                                <p class="text-xs text-secondary font-semibold pointer-events-none">
                                    <span x-text="previews.length"></span> photo(s) sélectionnée(s) — Cliquez pour changer
                                </p>
                            </template>

                            <input type="file" name="photos[]" x-ref="photosInput"
                                   multiple accept="image/*" class="hidden"
                                   @change="handleChange($event)"/>
                        </div>

                        {{-- Prévisualisation --}}
                        <div x-show="previews.length > 0" class="grid grid-cols-3 md:grid-cols-5 gap-4 mt-4">
                            <template x-for="(photo, index) in previews" :key="index">
                                <div class="relative">
                                    <img :src="photo.src" class="w-full aspect-square object-cover rounded-lg">
                                    <template x-if="photo.primary">
                                        <span class="absolute top-2 left-2 bg-secondary-container text-primary-container text-[10px] font-bold px-2 py-1 rounded">
                                            Principale
                                        </span>
                                    </template>
                                </div>
                            </template>
                        </div>

                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                            class="w-full gold-gradient text-primary-container py-5 rounded-xl font-black text-lg uppercase tracking-widest flex items-center justify-center gap-3 shadow-xl hover:brightness-110 active:scale-[0.98] transition-all">
                        Ajouter le véhicule
                        <span class="material-symbols-outlined">directions_car</span>
                    </button>

                </form>
            </div>

            <!-- Right Column: Sticky Info Panel -->
            <aside class="lg:sticky lg:top-28 space-y-6">
                <div class="bg-primary-container text-white rounded-xl p-8 overflow-hidden relative">
                    <h3 class="text-xl font-bold mb-8">Conseils pour bien renseigner</h3>
                    <div class="space-y-10 relative">
                        <div class="absolute left-4 top-2 bottom-2 border-l-2 border-dashed border-white/20"></div>
                        <div class="relative flex items-start gap-6">
                            <a href="#vehicle-step-1"
                                class="w-8 h-8 rounded-full flex items-center justify-center font-black shrink-0 relative z-10 transition-all"
                                :class="activeStep === 1 ? 'bg-secondary-container text-primary-container ring-4 ring-secondary-container/20' : 'bg-white/10 text-white border border-white/20'">
                                1
                            </a>
                            <div>
                                <p class="font-bold">Informations</p>
                                <p class="text-sm text-on-primary-container">Renseignez correctement les details et caracteristiques du vehicule.</p>
                            </div>
                        </div>
                        <div class="relative flex items-start gap-6">
                            <a href="#vehicle-step-2"
                                class="w-8 h-8 rounded-full flex items-center justify-center font-black shrink-0 relative z-10 transition-all"
                                :class="activeStep === 2 ? 'bg-secondary-container text-primary-container ring-4 ring-secondary-container/20' : 'bg-white/10 text-white border border-white/20'">
                                2
                            </a>
                            <div>
                                <p class="font-bold">Prix competitifs</p>
                                <p class="text-sm text-on-primary-container">Proposez des tarifs attractifs pour maximiser vos reservations.</p>
                            </div>
                        </div>
                        <div class="relative flex items-start gap-6">
                            <a href="#vehicle-step-3"
                                class="w-8 h-8 rounded-full flex items-center justify-center font-black shrink-0 relative z-10 transition-all"
                                :class="activeStep === 3 ? 'bg-secondary-container text-primary-container ring-4 ring-secondary-container/20' : 'bg-white/10 text-white border border-white/20'">
                                3
                            </a>
                            <div>
                                <p class="font-bold">Photos du vehicule</p>
                                <p class="text-sm text-on-primary-container">Ajoutez des photos nettes pour valoriser l'annonce avant publication.</p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-12 pt-8 border-t border-white/10 space-y-4">
                        <p class="text-xs font-bold uppercase tracking-widest text-on-primary-container mb-4">Checklist</p>
                        <ul class="space-y-3">
                            <li class="flex items-center gap-3 text-sm">
                                <span class="material-symbols-outlined text-secondary-container text-lg">check_circle</span>
                                Numéro d'immatriculation valide
                            </li>
                            <li class="flex items-center gap-3 text-sm">
                                <span class="material-symbols-outlined text-secondary-container text-lg">check_circle</span>
                                Au moins une photo
                            </li>
                            <li class="flex items-center gap-3 text-sm">
                                <span class="material-symbols-outlined text-secondary-container text-lg">check_circle</span>
                                Prix par jour obligatoire
                            </li>
                            <li class="flex items-center gap-3 text-sm">
                                <span class="material-symbols-outlined text-secondary-container text-lg">check_circle</span>
                                Description détaillée
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="bg-[#1A3C5E] rounded-xl p-6 flex gap-4 border border-white/5">
                    <span class="material-symbols-outlined text-secondary-container shrink-0" style="font-variation-settings: 'FILL' 1;">warning</span>
                    <p class="text-sm text-white/80 leading-relaxed">
                        <span class="font-bold text-white block mb-1">Important</span>
                        Assurez-vous que le numéro d'immatriculation est unique et correct avant de soumettre.
                    </p>
                </div>

                <div class="relative h-48 rounded-xl overflow-hidden group">
                    <img alt="Luxury car"
                         class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                         src="https://lh3.googleusercontent.com/aida-public/AB6AXuDiid2b84bma2EYs3GG0DUywKsAzbL4pXxgiATcETevEpNotcbqsiVDXT-NsOkFb9CO43n1gUTSRPe8W-va0OW7S7laTJsb1jH1Cy_v12LFwDQVOm1f5zByELQMYVS2Olq0X7jdBdX2ZHG99ZkEwRncjhPpjrn63ZLptJfpSpvZNTdZYHkLz1964lx7pUb0TOkI6FITT8CmMm65XR8pYWHhwOXfsZMMHols9jTJ7xovfP_nsrsGrxHcYrjKjBO4hGbVy3IbeVBSmnc"/>
                    <div class="absolute inset-0 bg-primary-container/40"></div>
                    <div class="absolute bottom-4 left-4 right-4">
                        <p class="text-white font-bold text-sm">Attirez plus de clients</p>
                        <p class="text-white/60 text-xs">Des annonces complètes reçoivent 3x plus de réservations.</p>
                    </div>
                </div>
            </aside>

        </div>
    </section>
</x-app-layout>
