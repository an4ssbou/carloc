@use('Illuminate\Support\Facades\Storage')
<x-app-layout title="Mon Profil">
    <main class="pt-16 pb-32">

        {{-- ═══════════════════════ HERO HEADER ═══════════════════════ --}}
        <section class="bg-primary-container py-12 px-6">
            <div class="max-w-7xl mx-auto">
                <div class="flex flex-col md:flex-row md:items-end justify-between gap-4">
                    <div>
                        <h1 class="text-4xl font-black text-white tracking-tight font-headline uppercase">
                            My Profile
                        </h1>
                        <p class="text-surface-container-high/60 text-sm mt-2 italic">
                            @if (session('status') === 'profile-updated')
                                Last saved: Just now
                            @else
                                Manage your personal information and documents
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </section>

        {{-- ═══════════════════════ GRID LAYOUT ═══════════════════════ --}}
        <div class="max-w-7xl mx-auto px-6 -mt-8 flex flex-col lg:grid lg:grid-cols-[1fr_380px] gap-8">

            {{-- ─────────────── LEFT COLUMN ─────────────── --}}
            <div class="flex flex-col gap-8">
                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                        @csrf
                    </form>
                @endif

                {{-- ════ FORMULAIRE PRINCIPAL (Photo + Infos + Documents) ════ --}}
                <form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data"
                    id="profile-form">
                    @csrf
                    @method('PATCH')

                    {{-- ── Card 1 : Profile Photo ── --}}
                    <div class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
                        <h2 class="text-lg font-bold mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary">photo_camera</span>
                            Profile Photo
                        </h2>
                        <div class="flex flex-col md:flex-row items-center gap-8">

                            {{-- Aperçu photo --}}
                            <div class="relative group flex-shrink-0">
                                <div
                                    class="w-32 h-32 rounded-full overflow-hidden border-4 border-surface-container-low ring-1 ring-outline-variant/20">
                                    @if ($user->profile_photo)
                                        <img id="profile-photo-preview" src="{{ Storage::url($user->profile_photo) }}"
                                            alt="Profile photo"
                                            class="w-full h-full object-cover profile-photo-preview" />
                                    @else
                                        <div id="profile-photo-initials"
                                            class="w-full h-full bg-primary-container flex items-center justify-center profile-photo-placeholder">
                                            <span class="text-white text-3xl font-black">
                                                {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <img id="profile-photo-preview" src="" alt=""
                                            class="w-full h-full object-cover profile-photo-preview hidden" />
                                    @endif
                                </div>
                                {{-- Bouton edit sur la photo --}}
                                <button type="button" onclick="document.getElementById('profile_photo_input').click()"
                                    class="absolute bottom-0 right-0 gold-gradient w-10 h-10 rounded-full flex items-center justify-center text-white shadow-lg hover:opacity-90 transition-opacity">
                                    <span class="material-symbols-outlined text-sm">edit</span>
                                </button>
                            </div>

                            {{-- Input + boutons --}}
                            <div class="flex-1 text-center md:text-left">
                                <p class="text-on-surface-variant text-sm mb-4 leading-relaxed">
                                    Upload a high-resolution portrait.<br />
                                    JPG, JPEG, PNG or WEBP accepted. Max size 2MB.
                                </p>
                                <div class="flex flex-wrap gap-3 justify-center md:justify-start">
                                    <button type="button"
                                        onclick="document.getElementById('profile_photo_input').click()"
                                        class="gold-gradient text-white px-6 py-2 rounded-lg text-sm font-bold shadow-sm hover:opacity-90 transition-opacity">
                                        Upload New
                                    </button>
                                    @if ($user->profile_photo)
                                        <button type="button" id="remove-profile-photo-btn"
                                            onclick="removeProfilePhoto()"
                                            class="bg-surface-container-low text-error px-6 py-2 rounded-lg text-sm font-medium hover:bg-error/10 transition-all">
                                            Remove
                                        </button>
                                    @endif
                                </div>
                                {{-- Nom du fichier sélectionné --}}
                                <p id="photo-file-name" class="mt-3 text-xs text-on-surface-variant hidden"></p>
                                <x-input-error :messages="$errors->get('profile_photo')" class="mt-2" />
                            </div>

                            {{-- Input file caché --}}
                            <input type="file" id="profile_photo_input" name="profile_photo"
                                accept="image/jpg,image/jpeg,image/png,image/webp" class="hidden"
                                onchange="previewProfilePhoto(this)" />
                            <input type="hidden" id="remove_profile_photo" name="remove_profile_photo"
                                value="0" />
                        </div>
                    </div>

                    {{-- ── Card 2 : Personal Information ── --}}
                    <div
                        class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] mt-8">
                        <h2 class="text-lg font-bold mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary">person</span>
                            Personal Information
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            {{-- First Name --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                    First Name <span class="text-secondary">*</span>
                                </label>
                                <input name="first_name" type="text"
                                    value="{{ old('first_name', $user->first_name) }}" required
                                    class="w-full bg-surface-container-high border-none rounded-lg p-3 text-on-surface focus:ring-2 focus:ring-secondary/50 outline-none transition-all" />
                                <x-input-error :messages="$errors->get('first_name')" class="mt-1" />
                            </div>

                            {{-- Last Name --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                    Last Name <span class="text-secondary">*</span>
                                </label>
                                <input name="last_name" type="text" value="{{ old('last_name', $user->last_name) }}"
                                    required
                                    class="w-full bg-surface-container-high border-none rounded-lg p-3 text-on-surface focus:ring-2 focus:ring-secondary/50 outline-none transition-all" />
                                <x-input-error :messages="$errors->get('last_name')" class="mt-1" />
                            </div>

                            {{-- Email --}}
                            <div class="space-y-2 col-span-1 md:col-span-2">
                                <label
                                    class="text-xs font-bold uppercase tracking-widest text-on-surface-variant flex justify-between items-center">
                                    Email Address <span class="text-secondary">*</span>
                                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                        <span
                                            class="text-error flex items-center gap-1 normal-case tracking-normal text-xs font-medium">
                                            <span class="material-symbols-outlined text-xs">warning</span>
                                            Unverified
                                        </span>
                                    @endif
                                </label>
                                <div class="relative">
                                    <input name="email" type="email" value="{{ old('email', $user->email) }}"
                                        required autocomplete="username"
                                        class="w-full bg-surface-container-high border-none rounded-lg p-3 text-on-surface pr-20 focus:ring-2 focus:ring-secondary/50 outline-none transition-all" />
                                    @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                        <button form="send-verification" type="submit"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-secondary font-bold text-xs hover:underline">
                                            Verify
                                        </button>
                                    @endif
                                </div>
                                @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                                    <p
                                        class="text-xs text-on-surface-variant bg-surface-container-low rounded-lg p-3 flex items-start gap-2">
                                        <span
                                            class="material-symbols-outlined text-sm text-secondary mt-0.5">info</span>
                                        Your email address is unverified. Click "Verify" to resend the verification
                                        link.
                                    </p>
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="text-xs text-green-600 font-medium">
                                            A new verification link has been sent to your email address.
                                        </p>
                                    @endif
                                @endif
                                <x-input-error :messages="$errors->get('email')" class="mt-1" />
                            </div>

                            {{-- Phone --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                    Phone Number
                                </label>
                                <input name="phone" type="tel" value="{{ old('phone', $user->phone) }}"
                                    placeholder="06XXXXXXXX"
                                    class="w-full bg-surface-container-high border-none rounded-lg p-3 text-on-surface focus:ring-2 focus:ring-secondary/50 outline-none transition-all" />
                                <x-input-error :messages="$errors->get('phone')" class="mt-1" />
                            </div>

                            {{-- CIN Number --}}
                            <div class="space-y-2">
                                <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                    CIN Number
                                </label>
                                <input name="cin_number" type="text"
                                    value="{{ old('cin_number', $user->cin_number) }}" placeholder="ex: AB123456"
                                    class="w-full bg-surface-container-high border-none rounded-lg p-3 text-on-surface font-mono focus:ring-2 focus:ring-secondary/50 outline-none transition-all" />
                                <x-input-error :messages="$errors->get('cin_number')" class="mt-1" />
                            </div>

                        </div>
                    </div>

                    {{-- ── Card 3 : Identity Documents ── --}}
                    <div
                        class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] border border-secondary/20 mt-8">
                        <h2 class="text-lg font-bold mb-2 flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary">verified_user</span>
                            Identity Documents
                        </h2>
                        <p class="text-sm text-on-surface-variant mb-6">
                            Your documents are stored in an encrypted vault accessible only for verification purposes.
                        </p>

                        {{-- Info box --}}
                        <div class="flex items-start gap-3 bg-secondary/5 rounded-lg p-4 mb-6">
                            <span class="material-symbols-outlined text-secondary text-sm mt-0.5">lock</span>
                            <p class="text-xs text-on-surface-variant leading-relaxed">
                                These documents are stored privately and are only accessible by you via a secure
                                temporary link.
                            </p>
                        </div>

                        <div class="space-y-4">

                            {{-- ─── CIN Photo ─── --}}
                            <div
                                class="flex items-center justify-between p-4 bg-surface-container-low rounded-lg flex-wrap gap-3">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded bg-white flex items-center justify-center text-secondary shadow-sm flex-shrink-0">
                                        <span class="material-symbols-outlined">badge</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm">National ID Card (CIN)</p>
                                    @if ($user->cin_photo)
                                            <p id="cin-status"
                                                class="text-xs text-green-600 flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-xs">check_circle</span>
                                                Document uploaded
                                            </p>
                                        @else
                                            <p class="text-xs text-on-surface-variant mt-0.5">
                                                JPG, PNG or PDF — Max 5MB
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if ($user->cin_photo)
                                        {{-- Bouton View --}}
                                        <div x-data="{ open: false }" id="cin-view-wrapper">
                                            <button type="button" @click="open = true"
                                                class="text-secondary hover:underline text-sm font-bold flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">visibility</span>
                                                View
                                            </button>

                                            {{-- Modal CIN --}}
                                            <div x-show="open" x-transition.opacity
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                                style="display:none;">
                                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                                                    @click="open = false"></div>
                                                <div
                                                    class="relative bg-white rounded-2xl shadow-2xl z-10 w-full max-w-4xl overflow-hidden">
                                                    <div
                                                        class="p-5 border-b border-surface-container-low flex justify-between items-center">
                                                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                                            <span
                                                                class="material-symbols-outlined text-secondary text-sm">badge</span>
                                                            National ID Card (CIN)
                                                        </h3>
                                                        <button type="button" @click="open = false"
                                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 hover:bg-red-200 text-red-600 font-bold transition-colors">
                                                            &times;
                                                        </button>
                                                    </div>
                                                    <div
                                                        class="p-6 bg-surface-container-low flex justify-center items-center h-[70vh] min-h-[420px] max-h-[760px]">
                                                        @php $ext = strtolower(pathinfo($user->cin_photo, PATHINFO_EXTENSION)); @endphp
                                                        @if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                                                            <img src="{{ route('profile.cin_photo') }}"
                                                                alt="CIN Document"
                                                                class="w-full h-full rounded-lg object-contain shadow-sm bg-white" />
                                                        @elseif ($ext === 'pdf')
                                                            <iframe src="{{ route('profile.cin_photo') }}"
                                                                class="w-full h-full rounded-lg border border-surface-container-high bg-white"></iframe>
                                                        @endif
                                                    </div>
                                                    <div
                                                        class="p-5 flex justify-between items-center border-t border-surface-container-low">
                                                        <button type="button" @click="open = false"
                                                            class="px-4 py-2 bg-surface-container-low hover:bg-surface-container-high text-on-surface-variant text-sm rounded-lg font-medium transition-colors">
                                                            Close
                                                        </button>
                                                        <a href="{{ route('profile.cin_photo') }}" download
                                                            class="px-4 py-2 gold-gradient text-white text-sm rounded-lg font-bold hover:opacity-90 transition-opacity flex items-center gap-2">
                                                            <span
                                                                class="material-symbols-outlined text-sm">download</span>
                                                            Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Bouton Replace --}}
                                        <label
                                            class="text-xs text-on-surface-variant underline cursor-pointer hover:text-secondary transition-colors font-medium">
                                            Replace
                                            <input type="file" id="cin_photo_input" name="cin_photo" accept=".jpg,.jpeg,.png,.pdf"
                                                class="hidden" onchange="prepareDocumentUpload('cin')" />
                                        </label>
                                        <button type="button" id="remove-cin-photo-btn"
                                            onclick="removeDocument('cin')"
                                            class="text-error hover:underline text-sm font-bold">
                                            Remove
                                        </button>
                                    @else
                                        {{-- Bouton Upload --}}
                                        <label
                                            class="bg-secondary/10 text-secondary px-4 py-2 rounded-lg text-xs font-bold hover:bg-secondary hover:text-white transition-all cursor-pointer flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">upload_file</span>
                                            Upload File
                                            <input type="file" id="cin_photo_input" name="cin_photo" accept=".jpg,.jpeg,.png,.pdf"
                                                class="hidden" onchange="prepareDocumentUpload('cin')" />
                                        </label>
                                    @endif
                                    <input type="hidden" id="remove_cin_photo" name="remove_cin_photo"
                                        value="0" />
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('cin_photo')" class="-mt-2 px-4" />

                            {{-- ─── Driver's License ─── --}}
                            <div
                                class="flex items-center justify-between p-4 bg-surface-container-low rounded-lg flex-wrap gap-3">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-10 h-10 rounded bg-white flex items-center justify-center text-secondary shadow-sm flex-shrink-0">
                                        <span class="material-symbols-outlined">directions_car</span>
                                    </div>
                                    <div>
                                        <p class="font-bold text-sm">Driver's License</p>
                                        @if ($user->license_photo)
                                            <p id="license-status"
                                                class="text-xs text-green-600 flex items-center gap-1 mt-0.5">
                                                <span class="material-symbols-outlined text-xs">check_circle</span>
                                                Document uploaded
                                            </p>
                                        @else
                                            <p class="text-xs text-on-surface-variant mt-0.5">
                                                JPG, PNG or PDF — Max 5MB
                                            </p>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-3">
                                    @if ($user->license_photo)
                                        {{-- Bouton View --}}
                                        <div x-data="{ open: false }" id="license-view-wrapper">
                                            <button type="button" @click="open = true"
                                                class="text-secondary hover:underline text-sm font-bold flex items-center gap-1">
                                                <span class="material-symbols-outlined text-sm">visibility</span>
                                                View
                                            </button>

                                            {{-- Modal License --}}
                                            <div x-show="open" x-transition.opacity
                                                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                                                style="display:none;">
                                                <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"
                                                    @click="open = false"></div>
                                                <div
                                                    class="relative bg-white rounded-2xl shadow-2xl z-10 w-full max-w-4xl overflow-hidden">
                                                    <div
                                                        class="p-5 border-b border-surface-container-low flex justify-between items-center">
                                                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                                                            <span
                                                                class="material-symbols-outlined text-secondary text-sm">directions_car</span>
                                                            Driver's License
                                                        </h3>
                                                        <button type="button" @click="open = false"
                                                            class="w-8 h-8 flex items-center justify-center rounded-full bg-red-100 hover:bg-red-200 text-red-600 font-bold transition-colors">
                                                            &times;
                                                        </button>
                                                    </div>
                                                    <div
                                                        class="p-6 bg-surface-container-low flex justify-center items-center h-[70vh] min-h-[420px] max-h-[760px]">
                                                        @php $extL = strtolower(pathinfo($user->license_photo, PATHINFO_EXTENSION)); @endphp
                                                        @if (in_array($extL, ['jpg', 'jpeg', 'png', 'webp']))
                                                            <img src="{{ route('profile.license_photo') }}"
                                                                alt="Driver License"
                                                                class="w-full h-full rounded-lg object-contain shadow-sm bg-white" />
                                                        @elseif ($extL === 'pdf')
                                                            <iframe src="{{ route('profile.license_photo') }}"
                                                                class="w-full h-full rounded-lg border border-surface-container-high bg-white"></iframe>
                                                        @endif
                                                    </div>
                                                    <div
                                                        class="p-5 flex justify-between items-center border-t border-surface-container-low">
                                                        <button type="button" @click="open = false"
                                                            class="px-4 py-2 bg-surface-container-low hover:bg-surface-container-high text-on-surface-variant text-sm rounded-lg font-medium transition-colors">
                                                            Close
                                                        </button>
                                                        <a href="{{ route('profile.license_photo') }}" download
                                                            class="px-4 py-2 gold-gradient text-white text-sm rounded-lg font-bold hover:opacity-90 transition-opacity flex items-center gap-2">
                                                            <span
                                                                class="material-symbols-outlined text-sm">download</span>
                                                            Download
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Bouton Replace --}}
                                        <label
                                            class="text-xs text-on-surface-variant underline cursor-pointer hover:text-secondary transition-colors font-medium">
                                            Replace
                                            <input type="file" id="license_photo_input" name="license_photo" accept=".jpg,.jpeg,.png,.pdf"
                                                class="hidden" onchange="prepareDocumentUpload('license')" />
                                        </label>
                                        <button type="button" id="remove-license-photo-btn"
                                            onclick="removeDocument('license')"
                                            class="text-error hover:underline text-sm font-bold">
                                            Remove
                                        </button>
                                    @else
                                        <label
                                            class="bg-secondary/10 text-secondary px-4 py-2 rounded-lg text-xs font-bold hover:bg-secondary hover:text-white transition-all cursor-pointer flex items-center gap-2">
                                            <span class="material-symbols-outlined text-sm">upload_file</span>
                                            Upload File
                                            <input type="file" id="license_photo_input" name="license_photo" accept=".jpg,.jpeg,.png,.pdf"
                                                class="hidden" onchange="prepareDocumentUpload('license')" />
                                        </label>
                                    @endif
                                    <input type="hidden" id="remove_license_photo" name="remove_license_photo"
                                        value="0" />
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('license_photo')" class="-mt-2 px-4" />

                        </div>
                    </div>

                    {{-- Bouton Save caché pour la bottom bar --}}
                    <button type="submit" id="profile-submit-btn" class="hidden"></button>

                </form>{{-- Fin formulaire principal --}}

                {{-- ════ FORMULAIRE MOT DE PASSE ════ --}}
                <form method="POST" action="{{ route('password.update') }}" id="password-form">
                    @csrf
                    @method('PUT')

                    {{-- ── Card 4 : Change Password ── --}}
                    <div class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
                        <h2 class="text-lg font-bold mb-6 flex items-center gap-2">
                            <span class="material-symbols-outlined text-secondary">lock</span>
                            Change Password
                        </h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            {{-- Inputs --}}
                            <div class="space-y-4">

                                {{-- Current Password --}}
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                        Current Password
                                    </label>
                                    <div class="relative">
                                        <input id="current_password_input" name="current_password" type="password"
                                            autocomplete="current-password"
                                            class="w-full bg-surface-container-high border-none rounded-lg p-3 text-on-surface pr-12 focus:ring-2 focus:ring-secondary/50 outline-none transition-all" />
                                        <button type="button"
                                            onclick="togglePassword('current_password_input', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface transition-colors">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-1" />
                                </div>

                                {{-- New Password --}}
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                        New Password
                                    </label>
                                    <div class="relative">
                                        <input id="new_password_input" name="password" type="password"
                                            autocomplete="new-password" placeholder="Enter new password"
                                            oninput="checkPasswordStrength(this.value)"
                                            class="w-full bg-surface-container-high border-none rounded-lg p-3 text-on-surface pr-12 focus:ring-2 focus:ring-secondary/50 outline-none transition-all" />
                                        <button type="button" onclick="togglePassword('new_password_input', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface transition-colors">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                        </button>
                                    </div>

                                    {{-- Barre de force --}}
                                    <div class="h-1.5 w-full bg-surface-container-high rounded-full overflow-hidden">
                                        <div id="strength-bar"
                                            class="h-full w-0 rounded-full transition-all duration-300"></div>
                                    </div>
                                    <p id="strength-label"
                                        class="text-[10px] font-medium uppercase tracking-wider text-on-surface-variant">
                                    </p>

                                    <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-1" />
                                </div>

                                {{-- Confirm Password --}}
                                <div class="space-y-2">
                                    <label class="text-xs font-bold uppercase tracking-widest text-on-surface-variant">
                                        Confirm New Password
                                    </label>
                                    <div class="relative">
                                        <input id="confirm_password_input" name="password_confirmation"
                                            type="password" autocomplete="new-password"
                                            placeholder="Repeat new password"
                                            class="w-full bg-surface-container-high border-none rounded-lg p-3 text-on-surface pr-12 focus:ring-2 focus:ring-secondary/50 outline-none transition-all" />
                                        <button type="button"
                                            onclick="togglePassword('confirm_password_input', this)"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-on-surface-variant hover:text-on-surface transition-colors">
                                            <span class="material-symbols-outlined text-sm">visibility</span>
                                        </button>
                                    </div>
                                    <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-1" />
                                </div>

                            </div>

                            {{-- Security requirements --}}
                            <div class="bg-surface-container-low p-6 rounded-xl border-l-4 border-secondary">
                                <h3 class="text-sm font-bold mb-4 uppercase tracking-wider">
                                    Security Requirements
                                </h3>
                                <ul class="space-y-3" id="requirements-list">
                                    <li id="req-length"
                                        class="flex items-center gap-3 text-sm text-on-surface-variant transition-colors">
                                        <span class="material-symbols-outlined text-sm">circle</span>
                                        At least 8 characters
                                    </li>
                                    <li id="req-upper"
                                        class="flex items-center gap-3 text-sm text-on-surface-variant transition-colors">
                                        <span class="material-symbols-outlined text-sm">circle</span>
                                        One uppercase letter
                                    </li>
                                    <li id="req-special"
                                        class="flex items-center gap-3 text-sm text-on-surface-variant transition-colors">
                                        <span class="material-symbols-outlined text-sm">circle</span>
                                        One special character
                                    </li>
                                    <li id="req-number"
                                        class="flex items-center gap-3 text-sm text-on-surface-variant transition-colors">
                                        <span class="material-symbols-outlined text-sm">circle</span>
                                        One numerical digit
                                    </li>
                                </ul>
                            </div>

                        </div>

                        {{-- Bouton update password --}}
                        <div class="mt-6 flex items-center gap-4">
                            <button type="submit"
                                class="gold-gradient text-white px-8 py-3 rounded-lg text-sm font-black uppercase tracking-widest flex items-center gap-2 shadow-lg hover:opacity-90 transition-opacity">
                                <span class="material-symbols-outlined text-sm">lock_reset</span>
                                Update Password
                            </button>
                            @if (session('status') === 'password-updated')
                                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                                    class="text-sm text-green-600 flex items-center gap-1">
                                    <span class="material-symbols-outlined text-sm">check_circle</span>
                                    Password updated!
                                </p>
                            @endif
                        </div>
                    </div>

                </form>{{-- Fin formulaire password --}}

                {{-- ════ DANGER ZONE ════ --}}
                <div class="bg-error/5 p-8 rounded-xl border border-error/20 mb-12">
                    <h2 class="text-lg font-bold text-error mb-2 flex items-center gap-2">
                        <span class="material-symbols-outlined">report_problem</span>
                        Danger Zone
                    </h2>
                    <p class="text-sm text-on-surface-variant mb-6">
                        Permanently delete your account and all associated data. This action cannot be undone.
                    </p>
                    <x-danger-button x-data=""
                        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                        class="border border-error text-error px-6 py-2 rounded-lg text-sm font-bold hover:bg-error hover:text-white transition-all">
                        Delete My Account
                    </x-danger-button>

                    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                        <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                            @csrf
                            @method('delete')

                            <div class="flex items-start gap-4 mb-4">
                                <div
                                    class="w-12 h-12 rounded-full bg-error/10 flex items-center justify-center flex-shrink-0">
                                    <span class="material-symbols-outlined text-error">report_problem</span>
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900">
                                        Delete your account?
                                    </h2>
                                    <p class="mt-1 text-sm text-gray-600">
                                        Once your account is deleted, all of its resources and data will be permanently
                                        deleted. Please enter your password to confirm.
                                    </p>
                                </div>
                            </div>

                            <div class="mt-6">
                                <x-input-label for="delete_password" value="{{ __('Password') }}" class="sr-only" />
                                <x-text-input id="delete_password" name="password" type="password"
                                    class="mt-1 block w-full" placeholder="{{ __('Enter your password') }}" />
                                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                            </div>

                            <div class="mt-6 flex justify-end gap-3">
                                <x-secondary-button x-on:click="$dispatch('close')">
                                    Cancel
                                </x-secondary-button>
                                <x-danger-button>
                                    Yes, Delete My Account
                                </x-danger-button>
                            </div>
                        </form>
                    </x-modal>
                </div>

            </div>{{-- Fin left column --}}

            {{-- ─────────────── RIGHT COLUMN (sticky) ─────────────── --}}
            <aside class="relative">
                <div class="lg:sticky lg:top-28 space-y-6">

                    {{-- Panel 1 : Profile Summary --}}
                    <div
                        class="bg-surface-container-lowest p-8 rounded-xl shadow-[0_20px_40px_rgba(13,33,55,0.06)] text-center">
                        <div class="relative w-28 h-28 mx-auto mb-6">
                            <div class="w-full h-full rounded-full overflow-hidden border-4 border-white shadow-lg">
                                @if ($user->profile_photo)
                                    <img src="{{ Storage::url($user->profile_photo) }}" alt="{{ $user->full_name }}"
                                        class="w-full h-full object-cover profile-photo-preview" />
                                @else
                                    <div
                                        class="w-full h-full bg-primary-container flex items-center justify-center profile-photo-placeholder">
                                        <span class="text-white text-3xl font-black">
                                            {{ strtoupper(substr($user->first_name, 0, 1)) }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                            <div
                                class="absolute -bottom-1 -right-1 gold-gradient w-8 h-8 rounded-full border-2 border-white flex items-center justify-center text-white text-xs">
                                @if ($user->email_verified_at)
                                    <span class="material-symbols-outlined text-sm">verified</span>
                                @else
                                    <span class="material-symbols-outlined text-sm">warning</span>
                                @endif
                            </div>
                        </div>

                        <h3 class="text-xl font-bold font-headline mb-1">
                            {{ $user->first_name }} {{ $user->last_name }}
                        </h3>
                        <p class="text-sm text-on-surface-variant mb-2">{{ $user->email }}</p>

                        <div
                            class="inline-block bg-secondary-fixed text-on-secondary-fixed px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest mb-4">
                            {{ ucfirst(str_replace('_', ' ', $user->role)) }}
                        </div>

                        @if ($user->email_verified_at)
                            <div
                                class="flex items-center justify-center gap-2 text-sm text-on-tertiary-container font-medium mb-6">
                                <span class="material-symbols-outlined text-base">verified_user</span>
                                Account Verified
                            </div>
                        @else
                            <div class="flex items-center justify-center gap-2 text-sm text-error font-medium mb-6">
                                <span class="material-symbols-outlined text-base">warning</span>
                                Email not verified
                            </div>
                        @endif

                        {{-- Documents status --}}
                        <div class="space-y-3 border-t border-surface-container-low pt-6">
                            <p
                                class="text-xs font-bold uppercase tracking-widest text-on-surface-variant text-left mb-3">
                                My Documents
                            </p>

                            {{-- CIN --}}
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-on-surface-variant flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">badge</span>
                                    ID Verification
                                </span>
                                @if ($user->cin_photo)
                                    <span
                                        class="bg-tertiary-fixed text-on-tertiary-fixed px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">check</span>
                                        Uploaded
                                    </span>
                                @else
                                    <span
                                        class="bg-error/10 text-error px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                                        Missing
                                    </span>
                                @endif
                            </div>

                            {{-- License --}}
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-medium text-on-surface-variant flex items-center gap-2">
                                    <span class="material-symbols-outlined text-sm">directions_car</span>
                                    Driver License
                                </span>
                                @if ($user->license_photo)
                                    <span
                                        class="bg-tertiary-fixed text-on-tertiary-fixed px-2 py-0.5 rounded text-[10px] font-bold flex items-center gap-1">
                                        <span class="material-symbols-outlined text-xs">check</span>
                                        Uploaded
                                    </span>
                                @else
                                    <span
                                        class="bg-error/10 text-error px-2 py-0.5 rounded text-[10px] font-bold uppercase">
                                        Missing
                                    </span>
                                @endif
                            </div>

                        </div>
                    </div>

                    {{-- Panel 2 : Help --}}
                    <div class="bg-primary-container p-6 rounded-xl text-white overflow-hidden relative">
                        <div class="relative z-10">
                            <h4 class="font-bold mb-2">Need assistance?</h4>
                            <p class="text-xs text-on-primary-container leading-relaxed mb-4">
                                Our team is available to help you with your account and documents.
                            </p>
                            <button
                                class="w-full bg-white text-primary-container py-3 rounded-lg text-xs font-black uppercase tracking-widest hover:bg-secondary-fixed transition-colors">
                                <a href="{{ route('contact') }}" class="text-primary-container">
                                    Contact Support
                                </a>
                            </button>
                        </div>
                        <span
                            class="material-symbols-outlined absolute -bottom-4 -right-4 text-8xl text-white/5 rotate-12">
                            headset_mic
                        </span>
                    </div>

                </div>
            </aside>

        </div>{{-- Fin grid --}}
    </main>

    {{-- ═══════════════════ BOTTOM ACTION BAR ═══════════════════ --}}
    <div
        class="fixed bottom-0 left-0 w-full bg-white/90 backdrop-blur-xl border-t border-surface-container-high z-50 px-6 py-4 flex items-center justify-center">
        <div class="max-w-7xl w-full flex items-center justify-between">

            <a href="{{ route('profile.edit') }}"
                class="text-on-surface-variant font-bold text-sm px-6 py-2 hover:bg-surface-container-low rounded-lg transition-all">
                Cancel
            </a>

            <div class="flex items-center gap-4">
                {{-- Toast succès --}}
                @if (session('status') === 'profile-updated')
                    <div x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)"
                        class="hidden md:flex items-center gap-3 bg-tertiary-container text-on-tertiary-container px-4 py-2 rounded-full text-xs font-medium shadow-sm">
                        <span class="material-symbols-outlined text-sm">check_circle</span>
                        Profile updated successfully
                    </div>
                @endif

                {{-- Bouton Save --}}
                <button type="button" onclick="document.getElementById('profile-submit-btn').click()"
                    class="gold-gradient text-white px-10 py-3 rounded-lg text-sm font-black uppercase tracking-widest flex items-center gap-2 shadow-xl shadow-secondary/20 hover:scale-[1.02] active:scale-95 transition-all">
                    Save Changes
                    <span class="material-symbols-outlined">done_all</span>
                </button>
            </div>

        </div>
    </div>

    {{-- ═══════════════════ SCRIPTS ═══════════════════ --}}
    <script>
        // ── Preview photo de profil ──────────────────────────────────
        function previewProfilePhoto(input) {
            if (input.files && input.files[0]) {
                const removeInput = document.getElementById('remove_profile_photo');
                if (removeInput) {
                    removeInput.value = '0';
                }

                const file = input.files[0];
                const reader = new FileReader();
                reader.onload = (e) => {
                    // Mettre à jour tous les aperçus (card + sidebar)
                    document.querySelectorAll('.profile-photo-preview').forEach(el => {
                        if (el.tagName === 'IMG') {
                            el.src = e.target.result;
                            el.classList.remove('hidden');
                        }
                    });
                    document.querySelectorAll('.profile-photo-placeholder').forEach(el => {
                        el.classList.add('hidden');
                    });
                    // Afficher le nom du fichier
                    const nameEl = document.getElementById('photo-file-name');
                    if (nameEl) {
                        nameEl.textContent = file.name + ' (' + (file.size / 1024).toFixed(0) + ' KB)';
                        nameEl.classList.remove('hidden');
                    }
                };
                reader.readAsDataURL(file);
            }
        }

        function removeProfilePhoto() {
            const fileInput = document.getElementById('profile_photo_input');
            const removeInput = document.getElementById('remove_profile_photo');
            const nameEl = document.getElementById('photo-file-name');

            if (fileInput) {
                fileInput.value = '';
            }

            if (removeInput) {
                removeInput.value = '1';
            }

            document.querySelectorAll('.profile-photo-preview').forEach(el => {
                if (el.tagName === 'IMG') {
                    el.src = '';
                    el.classList.add('hidden');
                }
            });
            document.querySelectorAll('.profile-photo-placeholder').forEach(el => {
                el.classList.remove('hidden');
            });

            if (nameEl) {
                nameEl.textContent = 'Profile photo will be removed when you save changes.';
                nameEl.classList.remove('hidden');
            }
        }

        function removeDocument(type) {
            const fileInput = document.getElementById(type + '_photo_input');
            const removeInput = document.getElementById('remove_' + type + '_photo');
            const statusEl = document.getElementById(type + '-status');
            const viewWrapper = document.getElementById(type + '-view-wrapper');
            const removeBtn = document.getElementById('remove-' + type + '-photo-btn');

            if (fileInput) {
                fileInput.value = '';
            }

            if (removeInput) {
                removeInput.value = '1';
            }

            if (statusEl) {
                statusEl.className = 'text-xs text-error flex items-center gap-1 mt-0.5';
                statusEl.innerHTML =
                    '<span class="material-symbols-outlined text-xs">delete</span>Document will be removed when you save';
            }

            if (viewWrapper) {
                viewWrapper.classList.add('hidden');
            }

            if (removeBtn) {
                removeBtn.classList.add('hidden');
            }
        }

        function prepareDocumentUpload(type) {
            const removeInput = document.getElementById('remove_' + type + '_photo');
            const statusEl = document.getElementById(type + '-status');
            const viewWrapper = document.getElementById(type + '-view-wrapper');
            const removeBtn = document.getElementById('remove-' + type + '-photo-btn');

            if (removeInput) {
                removeInput.value = '0';
            }

            if (statusEl) {
                statusEl.className = 'text-xs text-secondary flex items-center gap-1 mt-0.5';
                statusEl.innerHTML =
                    '<span class="material-symbols-outlined text-xs">upload</span>New file selected, save changes to apply';
            }

            if (viewWrapper) {
                viewWrapper.classList.remove('hidden');
            }

            if (removeBtn) {
                removeBtn.classList.remove('hidden');
            }
        }

        // ── Toggle visibilité mot de passe ──────────────────────────
        function togglePassword(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('.material-symbols-outlined');
            if (input.type === 'password') {
                input.type = 'text';
                icon.textContent = 'visibility_off';
            } else {
                input.type = 'password';
                icon.textContent = 'visibility';
            }
        }

        // ── Vérification force du mot de passe ──────────────────────
        function checkPasswordStrength(password) {
            const bar = document.getElementById('strength-bar');
            const label = document.getElementById('strength-label');

            const checks = {
                length: password.length >= 8,
                upper: /[A-Z]/.test(password),
                special: /[^A-Za-z0-9]/.test(password),
                number: /[0-9]/.test(password),
            };

            // Mettre à jour les requirements
            updateRequirement('req-length', checks.length);
            updateRequirement('req-upper', checks.upper);
            updateRequirement('req-special', checks.special);
            updateRequirement('req-number', checks.number);

            // Calculer le score
            const score = Object.values(checks).filter(Boolean).length;

            const levels = [{
                    width: '0%',
                    color: 'transparent',
                    text: ''
                },
                {
                    width: '25%',
                    color: '#EF4444',
                    text: 'Strength: Weak'
                },
                {
                    width: '50%',
                    color: '#F97316',
                    text: 'Strength: Fair'
                },
                {
                    width: '75%',
                    color: '#EAB308',
                    text: 'Strength: Good'
                },
                {
                    width: '100%',
                    color: '#22C55E',
                    text: 'Strength: Strong'
                },
            ];

            const level = password.length === 0 ? levels[0] : levels[score];
            bar.style.width = level.width;
            bar.style.backgroundColor = level.color;
            label.textContent = level.text;
            label.style.color = level.color;
        }

        function updateRequirement(id, met) {
            const el = document.getElementById(id);
            const icon = el.querySelector('.material-symbols-outlined');
            if (met) {
                el.classList.remove('text-on-surface-variant');
                el.classList.add('text-on-tertiary-container');
                icon.textContent = 'check_circle';
            } else {
                el.classList.remove('text-on-tertiary-container');
                el.classList.add('text-on-surface-variant');
                icon.textContent = 'circle';
            }
        }
    </script>

</x-app-layout>
