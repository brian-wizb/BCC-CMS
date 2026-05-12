<x-layouts.app title="Role Permissions">
    <section class="space-y-6">

        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.14);">
                    <i class="fas fa-shield-alt" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <div>
                    <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">Role Permissions</h3>
                    <p class="text-xs text-slate-500">Toggle permissions per role. System Admin always has full access.</p>
                </div>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex flex-wrap items-center gap-4 text-xs text-slate-500">
            <span class="flex items-center gap-1.5">
                <span class="inline-block h-4 w-4 rounded-sm bg-emerald-500/80"></span> Granted
            </span>
            <span class="flex items-center gap-1.5">
                <span class="inline-block h-4 w-4 rounded-sm bg-[var(--color-surface-200)]"></span> Not granted
            </span>
            <span class="flex items-center gap-1.5">
                <i class="fas fa-lock text-amber-400"></i> System Admin — immutable (always full access)
            </span>
        </div>

        @foreach ($grouped as $module => $modulePermissions)
            <article class="surface-card overflow-hidden">
                <div class="border-b border-[var(--color-surface-200)] px-5 py-3 flex items-center gap-2">
                    <i class="fas fa-puzzle-piece text-xs text-slate-400"></i>
                    <h4 class="text-sm font-semibold text-[var(--color-ink-950)] capitalize">{{ str_replace('_', ' ', $module) }}</h4>
                    <span class="ml-1 rounded-full bg-[var(--color-surface-200)] px-2 py-0.5 text-xs text-slate-500">{{ $modulePermissions->count() }} permissions</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="text-left text-xs uppercase text-slate-500 bg-[var(--color-surface-50)]">
                            <tr>
                                <th class="px-5 py-2.5 min-w-[220px]">Permission</th>
                                @foreach ($roles as $role)
                                    <th class="px-4 py-2.5 text-center whitespace-nowrap">
                                        {{ $role->name }}
                                        @if ($role->key === 'system_admin')
                                            <i class="fas fa-lock ml-1 text-amber-400 text-[10px]"></i>
                                        @endif
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-[var(--color-surface-200)]">
                            @foreach ($modulePermissions as $permission)
                                <tr class="hover:bg-[var(--color-surface-50)] transition-colors">
                                    <td class="px-5 py-2.5 font-mono text-xs text-slate-600">{{ $permission->key }}</td>
                                    @foreach ($roles as $role)
                                        @php
                                            $hasIt = $role->key === 'system_admin'
                                                || $role->permissions->contains('id', $permission->id);
                                        @endphp
                                        <td class="px-4 py-2.5 text-center">
                                            @if ($role->key === 'system_admin')
                                                {{-- System admin: read-only always-on indicator --}}
                                                <span class="inline-flex h-5 w-5 items-center justify-center rounded-sm bg-emerald-500/80 cursor-not-allowed" title="Always granted">
                                                    <i class="fas fa-check text-white text-[9px]"></i>
                                                </span>
                                            @else
                                                @if (auth()->user()->hasPermission('roles.update'))
                                                    <button type="button"
                                                        class="perm-toggle inline-flex h-5 w-5 items-center justify-center rounded-sm transition-colors {{ $hasIt ? 'bg-emerald-500/80 text-white' : 'bg-[var(--color-surface-200)] text-transparent' }}"
                                                        data-role="{{ $role->id }}"
                                                        data-permission="{{ $permission->id }}"
                                                        data-granted="{{ $hasIt ? '1' : '0' }}"
                                                        title="{{ $hasIt ? 'Revoke' : 'Grant' }} {{ $permission->key }} from {{ $role->name }}">
                                                        <i class="fas fa-check text-[9px]"></i>
                                                    </button>
                                                @else
                                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-sm {{ $hasIt ? 'bg-emerald-500/80 text-white' : 'bg-[var(--color-surface-200)] text-transparent' }}">
                                                        <i class="fas fa-check text-[9px]"></i>
                                                    </span>
                                                @endif
                                            @endif
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </article>
        @endforeach

    </section>

    @push('scripts')
    <script>
    (function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content
            ?? '{{ csrf_token() }}';

        document.querySelectorAll('.perm-toggle').forEach(btn => {
            btn.addEventListener('click', async function () {
                const roleId      = this.dataset.role;
                const permId      = this.dataset.permission;
                const wasGranted  = this.dataset.granted === '1';
                const nowGranted  = !wasGranted;

                this.disabled = true;

                try {
                    const res = await fetch(`/roles/${roleId}/permissions`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ permission_id: permId, grant: nowGranted }),
                    });

                    if (!res.ok) {
                        const err = await res.json().catch(() => ({}));
                        alert(err.message ?? 'Failed to update permission.');
                        return;
                    }

                    // Update state
                    this.dataset.granted = nowGranted ? '1' : '0';
                    if (nowGranted) {
                        this.classList.replace('bg-[var(--color-surface-200)]', 'bg-emerald-500/80');
                        this.classList.replace('text-transparent', 'text-white');
                        this.title = this.title.replace('Grant', 'Revoke');
                    } else {
                        this.classList.replace('bg-emerald-500/80', 'bg-[var(--color-surface-200)]');
                        this.classList.replace('text-white', 'text-transparent');
                        this.title = this.title.replace('Revoke', 'Grant');
                    }
                } catch (e) {
                    alert('Network error. Please try again.');
                } finally {
                    this.disabled = false;
                }
            });
        });
    })();
    </script>
    @endpush
</x-layouts.app>
