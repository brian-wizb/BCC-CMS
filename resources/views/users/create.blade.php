<x-layouts.app title="Create User">
    <section class="surface-card p-6 users-responsive">
        <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.14);">
                    <i class="fas fa-user-plus text-base" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Users</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Create new user</h3>
                </div>
            </div>
            <a href="{{ route('users.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                <i class="fas fa-arrow-left text-xs"></i> Back to users
            </a>
        </div>

        <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data" class="space-y-5" autocomplete="off">
            @csrf
            <div class="grid gap-4 md:grid-cols-1">
                {{-- Profile Photo --}}
                <div class="flex items-center gap-5">
                    <div id="photo-preview-wrap" class="relative h-20 w-20 flex-shrink-0">
                        <div id="photo-initials"
                             class="flex h-20 w-20 items-center justify-center rounded-2xl text-xl font-bold text-white"
                             style="background:linear-gradient(135deg,rgba(99,102,241,0.85),rgba(168,85,247,0.75));">
                            <i class="fas fa-user"></i>
                        </div>
                        <img id="photo-preview" src="" alt="Preview"
                             class="absolute inset-0 hidden h-20 w-20 rounded-2xl object-cover" />
                    </div>
                    <div class="flex-1">
                        <label class="form-label mb-1 block" for="profile_photo">Profile Photo <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input id="profile_photo" name="profile_photo" type="file" accept="image/jpeg,image/png,image/webp"
                               class="form-input cursor-pointer text-sm"
                               onchange="
                                   const f = this.files[0];
                                   if (f) {
                                       const url = URL.createObjectURL(f);
                                       const img = document.getElementById('photo-preview');
                                       const ini = document.getElementById('photo-initials');
                                       img.src = url;
                                       img.classList.remove('hidden');
                                       ini.classList.add('hidden');
                                   }
                               ">
                        @error('profile_photo')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-slate-400">JPG, PNG or WebP, max 2 MB. Will display on the topbar.</p>
                    </div>
                </div>

                <div>
                    <label class="form-label" for="leader_id">Link to leader <span class="text-slate-400 font-normal">(optional)</span></label>
                    <select id="leader_id" name="leader_id" class="form-input">
                        <option value="">- No leader link -</option>
                        @foreach ($leaders as $leader)
                            <option value="{{ $leader->id }}" @selected(old('leader_id') == $leader->id)>
                                {{ $leader->full_name }} @if($leader->member?->full_name) - Member: {{ $leader->member->full_name }} @endif
                            </option>
                        @endforeach
                    </select>
                    @error('leader_id')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-slate-400">If selected, this user account will sync with that leader for follow-up task assignments.</p>
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label" for="username">Username <span class="text-rose-400">*</span></label>
                    <input id="username" name="username" class="form-input" value="{{ old('username') }}" required autocomplete="off">
                    @error('username')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="full_name">Full Name (3 names)</label>
                    <input id="full_name" name="full_name" class="form-input" value="{{ old('full_name') }}">
                    @error('full_name')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label" for="email">Email</label>
                    <input id="email" name="email" type="email" class="form-input" value="{{ old('email') }}">
                    @error('email')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="form-label" for="role">Role <span class="text-rose-400">*</span></label>
                    <select id="role" name="role" class="form-input" required>
                        @foreach ($roles as $role)
                            <option value="{{ $role->key }}" @selected(old('role') === $role->key)>{{ $role->name }}</option>
                        @endforeach
                    </select>
                    @error('role')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <div>
                    <label class="form-label" for="password">Password <span class="text-rose-400">*</span></label>
                    <input id="password" name="password" type="password" class="form-input" required autocomplete="new-password">
                    @error('password')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    <p class="mt-1 text-xs text-slate-400">Min 8 chars, mixed case and number.</p>
                </div>
                <div>
                    <label class="form-label" for="password_confirmation">Confirm Password <span class="text-rose-400">*</span></label>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-input" required autocomplete="new-password">
                </div>
            </div>

            <div class="flex flex-wrap items-center justify-end gap-3 border-t border-[var(--color-surface-200)] pt-3">
                <a href="{{ route('users.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary flex items-center gap-1.5">
                    <i class="fas fa-user-plus text-sm"></i> Create user
                </button>
            </div>
        </form>
    </section>
</x-layouts.app>
