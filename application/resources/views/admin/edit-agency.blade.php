<x-admin-layout :pending-agencies-count="$pendingAgenciesCount" title="Edit Agency">
    <main class="ml-64 min-h-screen bg-surface">
        <header
            class="sticky top-0 z-40 flex h-16 w-full items-center justify-between bg-white/80 px-8 font-['Inter'] text-sm font-medium text-[#0D2137] backdrop-blur-md">
            <div class="flex flex-col">
                <nav class="mb-0.5 flex items-center space-x-2 text-xs text-slate-500">
                    <span>Dashboard</span>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <a href="{{ route('admin.agencies') }}" class="hover:text-primary-container">Agencies</a>
                    <span class="material-symbols-outlined text-[10px]">chevron_right</span>
                    <span class="font-bold text-[#815500]">Edit</span>
                </nav>
                <h2 class="text-lg font-extrabold tracking-tight">Edit Agency</h2>
            </div>
            <div class="flex items-center space-x-3 pl-6">
                <div class="text-right hidden sm:block">
                    <p class="text-[11px] font-bold text-[#0D2137]">{{ $adminUser?->full_name }}</p>
                    <p class="text-[10px] text-slate-500">Super Admin</p>
                </div>
                <img alt="Admin User Avatar" class="h-9 w-9 rounded-full border border-outline-variant/30 object-cover"
                    src="{{ $adminUser?->profile_photo_url }}" />
            </div>
        </header>

        <div class="mx-auto max-w-5xl p-8">
            <div class="rounded-2xl bg-surface-container-lowest p-8 shadow-[0_20px_40px_rgba(13,33,55,0.06)]">
                <form method="POST" action="{{ route('admin.agencies.update', $agency->slug) }}" enctype="multipart/form-data"
                    class="space-y-8">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="flex flex-col gap-2">
                            <label for="name" class="text-sm font-bold uppercase tracking-wider text-primary-container/70">Agency Name</label>
                            <input id="name" name="name" type="text" value="{{ old('name', $agency->name) }}"
                                class="w-full rounded-lg bg-surface-container-high px-4 py-3 outline-none transition-all focus:bg-white @error('name') ring-2 ring-error @enderror">
                            @error('name')
                                <p class="text-xs font-medium text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="city" class="text-sm font-bold uppercase tracking-wider text-primary-container/70">City</label>
                            <select id="city" name="city"
                                class="w-full appearance-none rounded-lg bg-surface-container-high px-4 py-3 outline-none transition-all focus:bg-white @error('city') ring-2 ring-error @enderror">
                                @foreach ($cities as $city)
                                    <option value="{{ $city['city'] }}" {{ old('city', $agency->city) === $city['city'] ? 'selected' : '' }}>
                                        {{ $city['city'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('city')
                                <p class="text-xs font-medium text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label for="address" class="text-sm font-bold uppercase tracking-wider text-primary-container/70">Address</label>
                            <input id="address" name="address" type="text" value="{{ old('address', $agency->address) }}"
                                class="w-full rounded-lg bg-surface-container-high px-4 py-3 outline-none transition-all focus:bg-white @error('address') ring-2 ring-error @enderror">
                            @error('address')
                                <p class="text-xs font-medium text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="phone" class="text-sm font-bold uppercase tracking-wider text-primary-container/70">Phone</label>
                            <input id="phone" name="phone" type="text" value="{{ old('phone', $agency->phone) }}"
                                class="w-full rounded-lg bg-surface-container-high px-4 py-3 outline-none transition-all focus:bg-white @error('phone') ring-2 ring-error @enderror">
                            @error('phone')
                                <p class="text-xs font-medium text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2">
                            <label for="email" class="text-sm font-bold uppercase tracking-wider text-primary-container/70">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email', $agency->email) }}"
                                class="w-full rounded-lg bg-surface-container-high px-4 py-3 outline-none transition-all focus:bg-white @error('email') ring-2 ring-error @enderror">
                            @error('email')
                                <p class="text-xs font-medium text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label for="logo" class="text-sm font-bold uppercase tracking-wider text-primary-container/70">Logo</label>
                            <input id="logo" name="logo" type="file" accept="image/png,image/jpg,image/jpeg"
                                class="w-full rounded-lg bg-surface-container-high px-4 py-3 outline-none transition-all file:mr-4 file:rounded-md file:border-0 file:bg-secondary-container file:px-3 file:py-2 file:font-semibold file:text-primary-container @error('logo') ring-2 ring-error @enderror">
                            <p class="text-xs text-slate-500">Choisir un nouveau logo est requis par la validation actuelle.</p>
                            @error('logo')
                                <p class="text-xs font-medium text-error">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex flex-col gap-2 md:col-span-2">
                            <label for="description" class="text-sm font-bold uppercase tracking-wider text-primary-container/70">Description</label>
                            <textarea id="description" name="description" rows="5"
                                class="w-full resize-none rounded-lg bg-surface-container-high px-4 py-3 outline-none transition-all focus:bg-white @error('description') ring-2 ring-error @enderror">{{ old('description', $agency->description) }}</textarea>
                            @error('description')
                                <p class="text-xs font-medium text-error">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('admin.agencies') }}"
                            class="rounded-lg border border-outline-variant px-5 py-3 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                            Cancel
                        </a>
                        <button type="submit"
                            class="gold-gradient rounded-lg px-6 py-3 text-sm font-bold text-primary-container shadow-lg shadow-secondary/20 transition hover:brightness-110">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>
</x-admin-layout>
