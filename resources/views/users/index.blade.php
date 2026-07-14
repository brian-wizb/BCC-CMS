<x-layouts.app title="Users">
    <style>
        .users-create-modal {
            align-items: center;
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        .users-modal-card {
            width: min(100%, 52rem);
            max-height: calc(100vh - 2rem);
            display: flex;
            flex-direction: column;
            margin: auto;
            overflow: hidden;
        }

        .users-modal-body {
            flex: 1 1 auto;
            min-height: 0;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            padding: 1.25rem 1.5rem;
        }

        .users-modal-actions {
            position: sticky;
            bottom: 0;
            background: color-mix(in srgb, var(--color-surface-50) 74%, transparent);
            backdrop-filter: blur(6px);
        }

        @media (max-width: 767px) {
            .users-create-modal {
                align-items: flex-start;
                padding: 0.75rem;
            }

            .users-modal-card {
                width: 100%;
                max-height: none;
                margin-top: 0.25rem;
            }

            .users-modal-body {
                padding: 1rem;
            }

            #modal-create-user .form-input,
            #modal-create-user .btn-primary,
            #modal-create-user .btn-secondary {
                min-height: 2.75rem;
            }

            #modal-create-user .users-modal-actions {
                display: grid;
                grid-template-columns: 1fr;
                gap: 0.5rem;
            }
        }
    </style>

    <section class="space-y-5 users-responsive">

        {{-- Header --}}
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.14);">
                    <i class="fas fa-users-cog" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">System Users</h3>
                    <p class="text-xs text-slate-500">All system access accounts are created and managed by the administrator.</p>
                </div>
            </div>
            @if (auth()->user()->hasPermission('users.create'))
                <a href="{{ route('users.create') }}"
                    class="btn-primary flex items-center gap-2">
                    <i class="fas fa-user-plus text-sm"></i> New user
                </a>
            @endif
        </div>

        {{-- Search / filter bar --}}
        <form method="GET" action="{{ route('users.index') }}" class="surface-card p-4">
            <div class="flex flex-wrap gap-3 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="form-label text-xs">Search</label>
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
                        <input name="search" class="form-input pl-8" placeholder="Username, name or email…" value="{{ $search ?? '' }}">
                    </div>
                </div>
                <button type="submit" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-filter text-xs"></i> Filter
                </button>
                @if ($search ?? false)
                    <a href="{{ route('users.index') }}" class="btn-secondary flex items-center gap-1.5 text-rose-400">
                        <i class="fas fa-times text-xs"></i> Clear
                    </a>
                @endif
            </div>
        </form>

        {{-- Users table --}}
        <div class="surface-card overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-xs uppercase text-slate-500">
                        <tr>
                            <th class="px-4 py-3">User</th>
                            <th class="px-4 py-3">Role</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Last login</th>
                            <th class="px-4 py-3">Created</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)]">
                        @forelse ($users as $user)
                            <tr class="hover:bg-[var(--color-surface-50)] transition-colors">
                                <td class="px-4 py-3.5">
                                    <p class="font-semibold text-[var(--color-ink-950)]">{{ $user->username }}</p>
                                    <p class="mt-0.5 text-xs text-slate-500">{{ $user->full_name ?: '—' }}</p>
                                    @if ($user->email)
                                        <p class="mt-0.5 text-xs text-slate-400">{{ $user->email }}</p>
                                    @endif
                                </td>
                                <td class="px-4 py-3.5">
                                    <x-ui.status-badge :status="$user->primaryRole()?->name ?? 'Unassigned'" tone="info" />
                                </td>
                                <td class="px-4 py-3.5">
                                    <x-ui.status-badge :status="$user->status" />
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-500">
                                    {{ $user->last_login_at?->format('d M Y H:i') ?? 'Never' }}
                                </td>
                                <td class="px-4 py-3.5 text-xs text-slate-500">
                                    {{ $user->created_at->format('d M Y') }}
                                </td>
                                <td class="px-4 py-3.5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        @if (auth()->user()->hasPermission('users.update'))
                                            <button type="button"
                                                onclick="openEditModal({{ json_encode([
                                                    'id'        => $user->id,
                                                    'full_name' => $user->full_name,
                                                    'email'     => $user->email,
                                                    'role'      => $user->primaryRole()?->key ?? '',
                                                    'status'    => $user->status,
                                                ]) }})"
                                                class="btn-secondary px-2.5 py-1.5 text-xs flex items-center gap-1">
                                                <i class="fas fa-pencil-alt"></i> Edit
                                            </button>
                                        @endif
                                        @if (auth()->user()->hasPermission('users.delete') && auth()->id() !== $user->id)
                                            <form method="POST" action="{{ route('users.destroy', $user) }}"
                                                data-confirm="Remove user {{ $user->username }}? They will be soft-deleted and can be restored.">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn-secondary px-2.5 py-1.5 text-xs text-rose-400 flex items-center gap-1">
                                                    <i class="fas fa-trash-alt"></i> Remove
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-10 text-center text-slate-400">
                                    @if ($search ?? false)
                                        No users found matching "{{ $search }}".
                                    @else
                                        No users yet. Create the first one.
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="border-t border-[var(--color-surface-200)] px-4 py-4">
                {{ $users->links() }}
            </div>
        </div>

    </section>

    {{-- ── EDIT USER MODAL ─────────────────────────────────────────────── --}}
    @if (auth()->user()->hasPermission('users.update'))
    <div id="modal-edit-user" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm p-4">
        <div class="surface-card w-full max-w-lg rounded-2xl shadow-2xl" onclick="event.stopPropagation()">
            <div class="flex items-center justify-between border-b border-[var(--color-surface-200)] px-6 py-4">
                <h4 class="text-base font-semibold text-[var(--color-ink-950)]">Edit user</h4>
                <button type="button" onclick="closeModal('modal-edit-user')" class="text-slate-400 hover:text-[var(--color-ink-950)] transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <form id="form-edit-user" method="POST" action="" class="p-6 space-y-4">
                @csrf
                @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="e_full_name">Full Name</label>
                        <input id="e_full_name" name="full_name" class="form-input">
                        @error('full_name')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="e_email">Email</label>
                        <input id="e_email" name="email" type="email" class="form-input">
                        @error('email')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="e_role">Role <span class="text-rose-400">*</span></label>
                        <select id="e_role" name="role" class="form-input" required>
                            @foreach ($roles as $role)
                                <option value="{{ $role->key }}">{{ $role->name }}</option>
                            @endforeach
                        </select>
                        @if (!auth()->user()->hasPermission('users.assign_roles'))
                            <p class="mt-1 text-xs text-amber-400"><i class="fas fa-lock mr-1"></i>You cannot change roles.</p>
                        @endif
                        @error('role')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label" for="e_status">Status <span class="text-rose-400">*</span></label>
                        <select id="e_status" name="status" class="form-input" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        @error('status')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="form-label" for="e_password">New Password <span class="text-slate-400 font-normal">(optional)</span></label>
                        <input id="e_password" name="password" type="password" class="form-input" autocomplete="new-password">
                        @error('password')<p class="mt-1 text-xs text-rose-400">{{ $message }}</p>@enderror
                        <p class="mt-1 text-xs text-slate-400">Leave blank to keep current password.</p>
                    </div>
                    <div>
                        <label class="form-label" for="e_password_confirmation">Confirm New Password</label>
                        <input id="e_password_confirmation" name="password_confirmation" type="password" class="form-input" autocomplete="new-password">
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 pt-2 border-t border-[var(--color-surface-200)]">
                    <button type="button" onclick="closeModal('modal-edit-user')" class="btn-secondary">Cancel</button>
                    <button type="submit" class="btn-primary flex items-center gap-1.5">
                        <i class="fas fa-save text-sm"></i> Save changes
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
    function openModal(id) {
        const el = document.getElementById(id);
        el.classList.remove('hidden');
        el.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    function closeModal(id) {
        const el = document.getElementById(id);
        el.classList.add('hidden');
        el.classList.remove('flex');
        document.body.style.overflow = '';
    }
    document.querySelectorAll('[id^="modal-"]').forEach(modal => {
        modal.addEventListener('click', function (e) {
            if (e.target === this) closeModal(this.id);
        });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('[id^="modal-"]').forEach(m => closeModal(m.id));
        }
    });

    function openEditModal(data) {
        document.getElementById('e_full_name').value = data.full_name ?? '';
        document.getElementById('e_email').value     = data.email ?? '';
        document.getElementById('e_status').value    = data.status ?? 'active';
        document.getElementById('e_password').value  = '';
        document.getElementById('e_password_confirmation').value = '';

        const roleSelect = document.getElementById('e_role');
        if (roleSelect) roleSelect.value = data.role ?? '';

        document.getElementById('form-edit-user').action = '/users/' + data.id;
        openModal('modal-edit-user');
    }

    @if ($errors->any())
        openModal('modal-edit-user');
    @endif
    </script>
    @endpush
</x-layouts.app>
