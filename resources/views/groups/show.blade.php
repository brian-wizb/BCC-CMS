@php
    $groupIconClass = str_contains($group->icon ?? '', 'fa-')
        ? (str_contains($group->icon ?? '', 'fas ') ? $group->icon : 'fas '.trim($group->icon))
        : 'fas fa-users';
@endphp

<x-layouts.app title="{{ $group->name }} Group">
    <section class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_380px]">

        {{-- Main column --}}
        <article class="surface-card p-6">

            {{-- Header --}}
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-start gap-4">
                    <a href="{{ route('groups.index') }}" class="mt-1 flex h-9 w-9 items-center justify-center rounded-lg hover:bg-[var(--color-surface-100)] transition" title="All groups">
                        <i class="fas fa-arrow-left text-slate-500 text-sm"></i>
                    </a>
                    <div class="flex items-center gap-3">
                        <span class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-xl"
                              style="background:{{ $group->color }}20; color:{{ $group->color }};">
                            <i class="{{ $groupIconClass }} text-xl"></i>
                        </span>
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Group</p>
                            <h3 class="text-2xl font-semibold text-[var(--color-ink-950)]">{{ $group->name }}</h3>
                        </div>
                    </div>
                </div>

                <div class="flex gap-2 print-hide">
                    @can('groups.update')
                        <a href="{{ route('groups.edit', $group) }}" class="btn-secondary flex items-center gap-1.5">
                            <i class="fas fa-pen text-xs"></i> Edit
                        </a>
                    @endcan
                    @can('groups.delete')
                        @if (! $group->is_predefined)
                            <form method="POST" action="{{ route('groups.destroy', $group) }}" data-confirm="Delete this group and all its members?">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-secondary flex items-center gap-1.5 text-red-500 hover:bg-red-50">
                                    <i class="fas fa-trash text-xs"></i> Delete
                                </button>
                            </form>
                        @endif
                    @endcan
                </div>
            </div>

            {{-- Description + meta --}}
            <div class="mt-5 grid gap-4 md:grid-cols-3">
                <div class="md:col-span-2 rounded-2xl border border-[var(--color-surface-200)] p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Description</p>
                    <p class="mt-2 text-sm text-slate-600 leading-relaxed">{{ $group->description ?: 'No description recorded.' }}</p>
                </div>
                <div class="rounded-2xl border border-[var(--color-surface-200)] p-4">
                    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-400">Members</p>
                    <p class="mt-2 text-3xl font-bold text-[var(--color-ink-950)]">{{ $group->memberships_count }}</p>
                </div>
            </div>

            {{-- Flash --}}
            @if (session('status'))
                <div class="mt-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                    <i class="fas fa-check-circle mr-1.5"></i>{{ session('status') }}
                </div>
            @endif

            {{-- Members table --}}
            <div class="mt-6 overflow-x-auto">
                <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                    <thead class="bg-[var(--color-surface-50)] text-left text-slate-500">
                        <tr>
                            <th class="px-4 py-3 font-medium">Name</th>
                            <th class="px-4 py-3 font-medium">Phone</th>
                            <th class="px-4 py-3 font-medium">Role</th>
                            <th class="px-4 py-3 font-medium">Joined</th>
                            <th class="px-4 py-3 font-medium">Notes</th>
                            <th class="px-4 py-3 font-medium">Type</th>
                            @can('groups.update')
                                <th class="px-4 py-3 font-medium">Actions</th>
                            @endcan
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                        @forelse ($group->memberships->sortBy(fn ($m) => $m->display_name) as $membership)
                            <tr class="hover:bg-[var(--color-surface-50)] transition">
                                <td class="px-4 py-3.5 font-medium text-[var(--color-ink-950)]">
                                    @if ($membership->member_id)
                                        <a href="{{ route('members.show', $membership->member) }}" class="hover:text-indigo-600 hover:underline flex items-center gap-1.5">
                                            {{ $membership->display_name }}
                                            <i class="fas fa-external-link-alt text-[10px] opacity-50"></i>
                                        </a>
                                    @else
                                        {{ $membership->display_name }}
                                    @endif
                                </td>
                                <td class="px-4 py-3.5 text-slate-500">{{ $membership->display_phone }}</td>
                                <td class="px-4 py-3.5">
                                    @php
                                        $roleColors = ['leader' => 'text-amber-700 bg-amber-50', 'coordinator' => 'text-blue-700 bg-blue-50', 'member' => 'text-slate-600 bg-slate-100'];
                                    @endphp
                                    <span class="rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $roleColors[$membership->role] ?? 'text-slate-600 bg-slate-100' }}">{{ ucfirst($membership->role) }}</span>
                                </td>
                                <td class="px-4 py-3.5 text-slate-500">{{ $membership->joined_at?->format('d M Y') ?: '—' }}</td>
                                <td class="px-4 py-3.5 text-slate-400 max-w-[160px] truncate text-xs">{{ $membership->notes ?: '—' }}</td>
                                <td class="px-4 py-3.5">
                                    @if ($membership->member_id)
                                        <span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[10px] font-semibold text-emerald-700">Registered</span>
                                    @else
                                        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500">Guest</span>
                                    @endif
                                </td>
                                @can('groups.update')
                                    <td class="px-4 py-3.5">
                                        <form method="POST" action="{{ route('groups.members.destroy', [$group, $membership]) }}" data-confirm="Remove {{ $membership->display_name }} from this group?">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-secondary text-xs py-1 px-2.5 text-red-500 hover:bg-red-50">Remove</button>
                                        </form>
                                    </td>
                                @endcan
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-10 text-center text-slate-400">
                                    <i class="fas fa-user-plus mb-2 block text-2xl text-slate-300"></i>
                                    No members yet. Use the add-member panel on the right.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </article>

        {{-- Right panel — Add member --}}
        <aside class="surface-card p-6">
            @can('groups.update')
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Add member</p>
            <h4 class="mt-1 text-lg font-semibold text-[var(--color-ink-950)]">Assign to {{ $group->name }}</h4>

            <form method="POST" action="{{ route('groups.members.store', $group) }}" class="mt-5 space-y-4" id="addMemberForm">
                @csrf

                {{-- Toggle: Registered / Guest --}}
                <div>
                    <label class="mb-2 block text-xs font-medium text-slate-500">Member type</label>
                    <div class="flex gap-2">
                        <button type="button" id="typeRegistered"
                                class="flex-1 rounded-lg border px-3 py-2 text-xs font-semibold transition toggle-btn active-toggle"
                                onclick="setMemberType('registered')">
                            <i class="fas fa-user-check mr-1"></i> Registered
                        </button>
                        <button type="button" id="typeGuest"
                                class="flex-1 rounded-lg border px-3 py-2 text-xs font-semibold transition toggle-btn"
                                onclick="setMemberType('guest')">
                            <i class="fas fa-user-plus mr-1"></i> Guest / Manual
                        </button>
                    </div>
                </div>

                {{-- Registered member selector --}}
                <div id="registeredBlock">
                    <label class="form-label" for="member_id">
                        <i class="fas fa-users mr-1 opacity-50 text-xs"></i> Select member
                    </label>
                    <select id="member_id" name="member_id" class="form-input">
                        <option value="">— Search/Select member —</option>
                        @foreach ($availableMembers as $m)
                            <option value="{{ $m->id }}" @selected(old('member_id') == $m->id)>
                                {{ $m->full_name }}{{ $m->tithe_code ? ' ('. $m->tithe_code .')' : '' }}
                                {{ $m->zone ? ' · ' . $m->zone : '' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Guest fields --}}
                <div id="guestBlock" class="hidden space-y-3">
                    <div>
                        <label class="form-label" for="guest_name">
                            <i class="fas fa-user mr-1 opacity-50 text-xs"></i> Full name <span class="text-red-500">*</span>
                        </label>
                        <input id="guest_name" name="guest_name" class="form-input" placeholder="e.g. James Mwanga" value="{{ old('guest_name') }}">
                    </div>
                    <div>
                        <label class="form-label" for="guest_phone">
                            <i class="fas fa-phone mr-1 opacity-50 text-xs"></i> Phone
                        </label>
                        <input id="guest_phone" name="guest_phone" class="form-input" placeholder="+255 7xx xxx xxx" value="{{ old('guest_phone') }}">
                    </div>
                </div>

                @error('member_id')
                    <p class="text-xs text-red-600">{{ $message }}</p>
                @enderror

                {{-- Role --}}
                <div>
                    <label class="form-label" for="role">
                        <i class="fas fa-tag mr-1 opacity-50 text-xs"></i> Role
                    </label>
                    <select id="role" name="role" class="form-input">
                        @foreach (['member' => 'Member', 'coordinator' => 'Coordinator', 'leader' => 'Leader'] as $value => $label)
                            <option value="{{ $value }}" @selected(old('role', 'member') === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Joined at --}}
                <div>
                    <label class="form-label" for="joined_at">
                        <i class="fas fa-calendar-check mr-1 opacity-50 text-xs"></i> Joined date
                    </label>
                    <input id="joined_at" name="joined_at" type="date" class="form-input" value="{{ old('joined_at', now()->toDateString()) }}">
                </div>

                {{-- Notes --}}
                <div>
                    <label class="form-label" for="notes">
                        <i class="fas fa-comment mr-1 opacity-50 text-xs"></i> Notes <span class="text-slate-400 text-xs">(optional)</span>
                    </label>
                    <textarea id="notes" name="notes" rows="2" class="form-input" placeholder="Any relevant notes...">{{ old('notes') }}</textarea>
                </div>

                <button type="submit" class="btn-primary w-full flex items-center justify-center gap-2">
                    <i class="fas fa-user-plus text-xs"></i> Add to group
                </button>
            </form>

            <div class="my-5 border-t border-[var(--color-surface-200)]"></div>

            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Bulk assign</p>
            <h4 class="mt-1 text-base font-semibold text-[var(--color-ink-950)]">Add multiple registered members</h4>

            <form method="POST" action="{{ route('groups.members.bulk', $group) }}" class="mt-4 space-y-3">
                @csrf

                <div>
                    <label class="form-label" for="member_ids_bulk">
                        <i class="fas fa-users mr-1 opacity-50 text-xs"></i> Select members (multiple)
                    </label>
                    <select id="member_ids_bulk" name="member_ids[]" class="form-input" multiple size="8" required>
                        @foreach ($availableMembers as $m)
                            <option value="{{ $m->id }}">
                                {{ $m->full_name }}{{ $m->tithe_code ? ' ('. $m->tithe_code .')' : '' }}{{ $m->zone ? ' · '.$m->zone : '' }}
                            </option>
                        @endforeach
                    </select>
                    <p class="mt-1 text-xs text-slate-400">Tip: hold <span class="font-semibold">Ctrl</span> to select multiple names.</p>
                </div>

                <div>
                    <label class="form-label" for="bulk_role">
                        <i class="fas fa-tag mr-1 opacity-50 text-xs"></i> Role for selected members
                    </label>
                    <select id="bulk_role" name="role" class="form-input">
                        <option value="member">Member</option>
                        <option value="coordinator">Coordinator</option>
                        <option value="leader">Leader</option>
                    </select>
                </div>

                <div>
                    <label class="form-label" for="bulk_joined_at">
                        <i class="fas fa-calendar-check mr-1 opacity-50 text-xs"></i> Joined date
                    </label>
                    <input id="bulk_joined_at" name="joined_at" type="date" class="form-input" value="{{ now()->toDateString() }}">
                </div>

                <div>
                    <label class="form-label" for="bulk_notes">
                        <i class="fas fa-comment mr-1 opacity-50 text-xs"></i> Notes (optional)
                    </label>
                    <textarea id="bulk_notes" name="notes" rows="2" class="form-input" placeholder="Applied to all selected members"></textarea>
                </div>

                <button type="submit" class="btn-secondary w-full flex items-center justify-center gap-2">
                    <i class="fas fa-layer-group text-xs"></i> Bulk add selected members
                </button>
            </form>
            @else
            <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Group tools</p>
            <h4 class="mt-1 text-lg font-semibold text-[var(--color-ink-950)]">Member management</h4>
            <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800">
                <p class="font-semibold"><i class="fas fa-lock mr-1.5"></i>You can view this group but cannot add or remove members.</p>
                <p class="mt-2 text-amber-700">Ask an administrator or secretary for the <span class="font-semibold">groups.update</span> permission if you need to manage group membership.</p>
            </div>
            @endcan
        </aside>

    </section>

    <style>
        .toggle-btn { background: var(--color-surface-50); border-color: var(--color-surface-200); color: var(--color-ink-700); }
        .active-toggle { background: rgba(99,102,241,0.1); border-color: rgba(99,102,241,0.4); color: rgba(99,102,241,1); }
    </style>

    <script>
        function setMemberType(type) {
            const registered = document.getElementById('registeredBlock');
            const guest      = document.getElementById('guestBlock');
            const btnR       = document.getElementById('typeRegistered');
            const btnG       = document.getElementById('typeGuest');
            const memberSel  = document.getElementById('member_id');
            const guestName  = document.getElementById('guest_name');

            if (type === 'registered') {
                registered.classList.remove('hidden');
                guest.classList.add('hidden');
                btnR.classList.add('active-toggle');
                btnG.classList.remove('active-toggle');
                if (memberSel) memberSel.disabled = false;
                if (guestName) guestName.disabled = true;
            } else {
                registered.classList.add('hidden');
                guest.classList.remove('hidden');
                btnR.classList.remove('active-toggle');
                btnG.classList.add('active-toggle');
                if (memberSel) { memberSel.disabled = true; memberSel.value = ''; }
                if (guestName) guestName.disabled = false;
            }
        }

        // Restore type on validation error
        @if (old('guest_name'))
            setMemberType('guest');
        @else
            setMemberType('registered');
        @endif
    </script>
</x-layouts.app>
