<x-app-layout :title="'Modifier ' . $vehicle->brand . ' ' . $vehicle->model">

    {{-- Header --}}
    <section class="bg-primary-container pt-16 pb-20 relative overflow-hidden">
        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <nav class="flex items-center space-x-2 text-on-primary-container text-sm mb-6">
                <a href="{{ route('vehicles.index') }}" class="hover:text-white transition-colors">Véhicules</a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <a href="{{ route('vehicles.show', $vehicle) }}" class="hover:text-white transition-colors">{{ $vehicle->brand }} {{ $vehicle->model }}</a>
                <span class="material-symbols-outlined text-xs">chevron_right</span>
                <span class="text-white/60">Modifier</span>
            </nav>
            <h1 class="text-4xl md:text-5xl font-black text-white mb-4 tracking-tight">Modifier le véhicule</h1>
            <p class="text-on-primary-container max-w-2xl text-lg">
                Modifiez les informations de votre véhicule.
            </p>
            <div class="w-24 h-1 bg-secondary-container mt-8"></div>
        </div>
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/2 w-96 h-96 bg-secondary/10 rounded-full blur-3xl"></div>
    </section>

    {{-- Formulaire --}}
    <section class="max-w-7xl mx-auto px-6 -mt-12 mb-24">
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_400px] gap-8 items-start">

            {{-- Colonne gauche : formulaire --}}
            <div class="bg-surface-container-lowest rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] p-8 md:p-12">

                @if($errors->any())
                    <div class="mb-8 bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li class="text-sm">{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('vehicles.update', $vehicle) }}" method="POST" enctype="multipart/form-data" class="space-y-12">
                    @csrf
                    @method('PUT')

                    {{-- Section 1: Informations générales --}}
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary-container">directions_car</span>
                            </div>
                            <h2 class="text-2xl font-bold text-primary-container">Informations générales</h2>
                        </div>

                        {{-- Agence --}}
                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">Agence</label>
                            <div class="w-full bg-surface-container-high rounded-lg px-4 py-3 text-on-surface font-semibold flex items-center gap-2">
                                <span class="material-symbols-outlined text-secondary text-lg">store</span>
                                {{ $vehicle->agency->name }}
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="brand">Marque *</label>
                                <input id="brand" name="brand" value="{{ old('brand', $vehicle->brand) }}"
                                       type="text" placeholder="Ex: Mercedes-Benz"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('brand') ring-2 ring-error @enderror"/>
                                @error('brand') <p class="text-xs text-error font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="model">Modèle *</label>
                                <input id="model" name="model" value="{{ old('model', $vehicle->model) }}"
                                       type="text" placeholder="Ex: C-Class"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('model') ring-2 ring-error @enderror"/>
                                @error('model') <p class="text-xs text-error font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="year">Année *</label>
                                <input id="year" name="year" value="{{ old('year', $vehicle->year) }}"
                                       type="number" min="1990" max="{{ now()->year }}"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('year') ring-2 ring-error @enderror"/>
                                @error('year') <p class="text-xs text-error font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="color">Couleur *</label>
                                <input id="color" name="color" value="{{ old('color', $vehicle->color) }}"
                                       type="text" placeholder="Ex: Noir"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('color') ring-2 ring-error @enderror"/>
                                @error('color') <p class="text-xs text-error font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="mileage">Kilométrage</label>
                                <input id="mileage" name="mileage" value="{{ old('mileage', $vehicle->mileage) }}"
                                       type="number" min="0"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none"/>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider" for="seats">Nombre de places *</label>
                                <input id="seats" name="seats" value="{{ old('seats', $vehicle->seats) }}"
                                       type="number" min="1" max="20"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('seats') ring-2 ring-error @enderror"/>
                                @error('seats') <p class="text-xs text-error font-medium">{{ $message }}</p> @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Section 2: Caractéristiques techniques --}}
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary-container">settings</span>
                            </div>
                            <h2 class="text-2xl font-bold text-primary-container">Caractéristiques techniques</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">État *</label>
                                <select name="condition" class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none appearance-none">
                                    <option value="new" {{ old('condition', $vehicle->condition) == 'new' ? 'selected' : '' }}>Neuf</option>
                                    <option value="good" {{ old('condition', $vehicle->condition) == 'good' ? 'selected' : '' }}>Bon état</option>
                                    <option value="used" {{ old('condition', $vehicle->condition) == 'used' ? 'selected' : '' }}>Usagé</option>
                                </select>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">Carburant *</label>
                                <select name="fuel_type" class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none appearance-none">
                                    <option value="gasoline" {{ old('fuel_type', $vehicle->fuel_type) == 'gasoline' ? 'selected' : '' }}>Essence</option>
                                    <option value="diesel" {{ old('fuel_type', $vehicle->fuel_type) == 'diesel' ? 'selected' : '' }}>Diesel</option>
                                    <option value="electric" {{ old('fuel_type', $vehicle->fuel_type) == 'electric' ? 'selected' : '' }}>Électrique</option>
                                    <option value="hybrid" {{ old('fuel_type', $vehicle->fuel_type) == 'hybrid' ? 'selected' : '' }}>Hybride</option>
                                </select>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">Transmission *</label>
                                <select name="transmission" class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none appearance-none">
                                    <option value="manual" {{ old('transmission', $vehicle->transmission) == 'manual' ? 'selected' : '' }}>Manuelle</option>
                                    <option value="automatic" {{ old('transmission', $vehicle->transmission) == 'automatic' ? 'selected' : '' }}>Automatique</option>
                                </select>
                            </div>

                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">Statut *</label>
                            <select name="status" class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none appearance-none">
                                <option value="available" {{ old('status', $vehicle->status) == 'available' ? 'selected' : '' }}>Disponible</option>
                                <option value="unavailable" {{ old('status', $vehicle->status) == 'unavailable' ? 'selected' : '' }}>Indisponible</option>
                            </select>
                        </div>

                        <div class="flex flex-col gap-2">
                            <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">Description</label>
                            <textarea name="description" rows="4"
                                      placeholder="Décrivez le véhicule..."
                                      class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none resize-none">{{ old('description', $vehicle->description) }}</textarea>
                        </div>

                        <input type="hidden" name="has_gps" value="0">
                        <label class="flex items-center gap-4 p-4 rounded-lg bg-surface hover:bg-surface-container-low transition-colors cursor-pointer group">
                            <input type="checkbox" name="has_gps" value="1"
                                   {{ old('has_gps', $vehicle->has_gps) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-outline-variant text-secondary focus:ring-secondary"/>
                            <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors font-medium">
                                Ce véhicule est équipé d’un GPS de géolocalisation
                            </span>
                        </label>
                        <p class="text-sm text-on-surface-variant">Désactiver le GPS efface la dernière position et révoque la clé du boîtier.</p>
                        @if($vehicle->has_gps)
                            <a href="{{ route('agency.vehicles.gps', $vehicle) }}" class="inline-flex text-sm font-bold text-secondary underline">Ouvrir le suivi GPS</a>
                        @endif

                        <label class="flex items-center gap-4 p-4 rounded-lg bg-surface hover:bg-surface-container-low transition-colors cursor-pointer group">
                            <input type="checkbox" name="is_featured" value="1"
                                   {{ old('is_featured', $vehicle->is_featured) ? 'checked' : '' }}
                                   class="w-5 h-5 rounded border-outline-variant text-secondary focus:ring-secondary"/>
                            <span class="text-sm text-on-surface-variant group-hover:text-on-surface transition-colors font-medium">
                                Mettre ce véhicule en vedette pour l'agence. Si un autre véhicule est déjà en vedette, il sera remplacé.
                            </span>
                        </label>
                    </div>

                    {{-- Section 3: Tarifs --}}
                    <div class="space-y-8">
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary-container">payments</span>
                            </div>
                            <h2 class="text-2xl font-bold text-primary-container">Tarifs (en DH)</h2>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">Prix par jour *</label>
                                <input name="price_per_day" value="{{ old('price_per_day', $vehicle->price_per_day) }}"
                                       type="number" min="0" step="0.01"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none @error('price_per_day') ring-2 ring-error @enderror"/>
                                @error('price_per_day') <p class="text-xs text-error font-medium">{{ $message }}</p> @enderror
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">Prix par semaine</label>
                                <input name="price_week" value="{{ old('price_week', $vehicle->price_week) }}"
                                       type="number" min="0" step="0.01"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none"/>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">Prix pour 2 semaines</label>
                                <input name="price_two_weeks" value="{{ old('price_two_weeks', $vehicle->price_two_weeks) }}"
                                       type="number" min="0" step="0.01"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none"/>
                            </div>

                            <div class="flex flex-col gap-2">
                                <label class="text-sm font-bold text-primary-container/70 uppercase tracking-wider">Prix par mois</label>
                                <input name="price_month" value="{{ old('price_month', $vehicle->price_month) }}"
                                       type="number" min="0" step="0.01"
                                       class="w-full bg-surface-container-high border-none rounded-lg px-4 py-3 focus:bg-white transition-all outline-none"/>
                            </div>

                        </div>
                    </div>

                    {{-- Section 4: Photos --}}
                    <div class="space-y-8"
                         x-data='{
                            previews: [],
                            deletedPhotoIds: [],
                            removeExistingPhoto(photoId) {
                                if (!this.deletedPhotoIds.includes(photoId)) {
                                    this.deletedPhotoIds.push(photoId);
                                }
                            },
                            restoreExistingPhoto(photoId) {
                                this.deletedPhotoIds = this.deletedPhotoIds.filter(id => id !== photoId);
                            },
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
                         }'>
                        <div class="flex items-center gap-4">
                            <div class="w-10 h-10 rounded-lg bg-surface-container-high flex items-center justify-center">
                                <span class="material-symbols-outlined text-primary-container">photo_library</span>
                            </div>
                            <h2 class="text-2xl font-bold text-primary-container">Photos du véhicule</h2>
                        </div>

                        {{-- Photos existantes --}}
                        @if($vehicle->photos->count() > 0)
                        <div>
                            <p class="text-sm font-bold text-on-surface-variant uppercase tracking-wider mb-4">Photos actuelles</p>
                            <div class="grid grid-cols-3 md:grid-cols-5 gap-4">
                                @foreach($vehicle->photos as $photo)
                                <div class="relative group" x-show="!deletedPhotoIds.includes({{ $photo->id }})">
                                    <img src="{{ asset('storage/' . $photo->path) }}"
                                         class="w-full aspect-square object-cover rounded-lg">
                                    @if($photo->is_primary)
                                        <span class="absolute top-2 left-2 bg-secondary-container text-primary-container text-[10px] font-bold px-2 py-1 rounded">
                                            Principale
                                        </span>
                                    @endif
                                    <button type="button"
                                        @click="removeExistingPhoto({{ $photo->id }})"
                                        class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition-opacity bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs hover:bg-red-600">
                                        <span class="material-symbols-outlined text-sm">close</span>
                                    </button>
                                </div>
                                @endforeach
                            </div>

                            <div x-show="deletedPhotoIds.length > 0" class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3">
                                <p class="text-sm font-semibold text-amber-800">
                                    <span x-text="deletedPhotoIds.length"></span> photo(s) seront supprimee(s) apres avoir sauvegarde les modifications.
                                </p>
                                <div class="mt-3 flex flex-wrap gap-2">
                                    @foreach($vehicle->photos as $photo)
                                        <button type="button"
                                            x-show="deletedPhotoIds.includes({{ $photo->id }})"
                                            @click="restoreExistingPhoto({{ $photo->id }})"
                                            class="rounded-full bg-white px-3 py-1 text-xs font-bold text-amber-800 shadow-sm">
                                            Restaurer {{ $photo->id }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        @endif

                        <template x-for="photoId in deletedPhotoIds" :key="photoId">
                            <input type="hidden" name="delete_photo_ids[]" :value="photoId">
                        </template>

                        {{-- Ajouter nouvelles photos --}}
                        <div class="border-2 border-dashed rounded-xl p-8 flex flex-col items-center justify-center text-center bg-surface transition-all cursor-pointer group min-h-[140px]"
                             :class="previews.length > 0 ? 'border-secondary' : 'border-outline-variant/30 hover:bg-surface-container-low'"
                             @click="$refs.photosInput.click()">

                            <template x-if="previews.length === 0">
                                <div class="flex flex-col items-center pointer-events-none">
                                    <span class="material-symbols-outlined text-4xl text-outline mb-4 group-hover:text-secondary transition-colors">add_photo_alternate</span>
                                    <p class="text-sm text-on-surface-variant mb-1 font-semibold">Ajouter de nouvelles photos</p>
                                    <p class="text-xs text-outline">JPG, PNG, WEBP • Max 2MB chacune</p>
                                </div>
                            </template>

                            <template x-if="previews.length > 0">
                                <p class="text-xs text-secondary font-semibold pointer-events-none">
                                    <span x-text="previews.length"></span> nouvelle(s) photo(s) — Cliquez pour changer
                                </p>
                            </template>

                            <input type="file" name="photos[]" x-ref="photosInput"
                                   multiple accept="image/*" class="hidden"
                                   @change="handleChange($event)"/>
                        </div>

                        <div x-show="previews.length > 0" class="grid grid-cols-3 md:grid-cols-5 gap-4">
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

                    {{-- Bouton submit --}}
                    <button type="submit"
                            class="w-full gold-gradient text-primary-container py-5 rounded-xl font-black text-lg uppercase tracking-widest flex items-center justify-center gap-3 shadow-xl hover:brightness-110 active:scale-[0.98] transition-all">
                        Sauvegarder les modifications
                        <span class="material-symbols-outlined">save</span>
                    </button>

                </form>
            </div>

            {{-- Colonne droite sticky --}}
            <aside class="lg:sticky lg:top-28 space-y-6">

                {{-- Aperçu actuel --}}
                <div class="bg-primary-container text-white rounded-xl p-8">
                    <h3 class="text-xl font-bold mb-6">Aperçu actuel</h3>
                    <div class="aspect-video rounded-lg overflow-hidden mb-4">
                        <img src="{{ $vehicle->primary_photo_url }}"
                             alt="{{ $vehicle->brand }} {{ $vehicle->model }}"
                             class="w-full h-full object-cover">
                    </div>
                    <p class="font-bold text-lg">{{ $vehicle->brand }} {{ $vehicle->model }}</p>
                    <p class="text-on-primary-container text-sm">{{ $vehicle->year }} · {{ $vehicle->fuel_type_label }}</p>
                    <div class="mt-4 pt-4 border-t border-white/10">
                        <span class="text-secondary-container text-2xl font-black">{{ $vehicle->price_per_day }} DH</span>
                        <span class="text-on-primary-container text-sm">/jour</span>
                    </div>
                </div>

                {{-- Danger zone --}}
                <div class="bg-red-50 rounded-xl p-6 border border-red-200">
                    <h3 class="text-lg font-bold text-red-700 mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined">warning</span>
                        Zone dangereuse
                    </h3>
                    <p class="text-sm text-red-600 mb-4">La suppression est irréversible. Toutes les données seront perdues.</p>
                    <form action="{{ route('vehicles.destroy', $vehicle) }}" method="POST"
                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce véhicule ? Cette action est irréversible.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full bg-red-600 text-white py-3 rounded-lg font-bold hover:bg-red-700 transition-colors flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined">delete</span>
                            Supprimer ce véhicule
                        </button>
                    </form>
                </div>

            </aside>
        </div>
    </section>
</x-app-layout>
