<x-layouts.app title="Families">
    <section class="surface-card p-6">

        {{-- Header --}}
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl"
                      style="background:rgba(36,184,255,0.12);">
                    <i class="fas fa-home text-sm" style="color:rgba(36,184,255,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Church families</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">
                        Families
                        <span class="ml-1.5 rounded-full px-2 py-0.5 text-sm font-medium"
                              style="background:rgba(36,184,255,0.12); color:rgba(36,184,255,0.9);">{{ $families->total() }}</span>
                    </h3>
                </div>
            </div>

            <div class="flex flex-wrap gap-2">
                <a href="{{ route('families.create') }}" class="btn-primary flex items-center gap-1.5">
                    <i class="fas fa-plus text-xs"></i> Add family
                </a>
                <a href="{{ route('families.export') }}" class="btn-secondary flex items-center gap-1.5">
                    <i class="fas fa-file-csv text-xs"></i> Export CSV
                </a>
            </div>
        </div>

        {{-- Search + Import --}}
        <div class="mt-5 flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
            <form method="GET" action="{{ route('families.index') }}" class="flex w-full gap-2 lg:max-w-md">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400"></i>
                    <input name="search" class="form-input pl-8 text-sm" placeholder="Search name, phone, zone…" value="{{ $search }}">
                </div>
                <button type="submit" class="btn-secondary text-sm">Search</button>
            </form>

            <form method="POST" action="{{ route('families.import') }}" enctype="multipart/form-data" class="flex gap-2">
                @csrf
                <input type="file" name="file" accept=".csv,text/csv" class="form-input max-w-xs text-sm">
                <button type="submit" class="btn-secondary flex items-center gap-1.5 text-sm">
                    <i class="fas fa-upload text-xs"></i> Import
                </button>
            </form>
        </div>

        {{-- Table --}}
        <div class="mt-5 overflow-x-auto">
            <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-[0.12em] text-slate-400">
                    <tr>
                        <th class="px-5 py-3"><i class="fas fa-user-tie mr-1.5 opacity-60"></i>Head of family</th>
                        <th class="px-5 py-3"><i class="fas fa-venus-mars mr-1.5 opacity-60"></i>Gender</th>
                        <th class="px-5 py-3"><i class="fas fa-phone mr-1.5 opacity-60"></i>Phone</th>
                        <th class="px-5 py-3"><i class="fas fa-map-marker-alt mr-1.5 opacity-60"></i>Zone</th>
                        <th class="px-5 py-3"><i class="fas fa-users mr-1.5 opacity-60"></i>Members</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-[var(--color-surface-200)]">
                    @forelse ($families as $family)
                        <tr class="transition hover:bg-[var(--color-surface-50)]">
                            <td class="px-5 py-3">
                                <span class="flex items-center gap-2">
                                    <span class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold"
                                          style="background:rgba(36,184,255,0.12); color:rgba(36,184,255,0.9);">
                                        {{ mb_strtoupper(mb_substr($family->head_of_family, 0, 1)) }}
                                    </span>
                                    <span class="font-medium text-[var(--color-ink-950)]">{{ $family->head_of_family }}</span>
                                </span>
                            </td>
                            <td class="px-5 py-3 text-slate-500">
                                @if (strtolower((string) $family->gender) === 'male')
                                    <i class="fas fa-mars mr-1 text-sky-400"></i>
                                @elseif (strtolower((string) $family->gender) === 'female')
                                    <i class="fas fa-venus mr-1 text-pink-400"></i>
                                @endif
                                {{ $family->gender ?: '—' }}
                            </td>
                            <td class="px-5 py-3 text-slate-500">{{ $family->phone ?: '—' }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $family->zone ?? '—' }}</td>
                            <td class="px-5 py-3">
                                <span class="inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-semibold"
                                      style="background:rgba(52,211,153,0.12); color:rgba(52,211,153,0.9);">
                                    <i class="fas fa-users text-[10px]"></i>
                                    {{ $family->members_count }}
                                </span>
                            </td>
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-1.5">
                                    <a href="{{ route('families.show', $family) }}"
                                       class="btn-secondary flex items-center gap-1 px-2.5 py-1 text-xs">
                                        <i class="fas fa-eye text-[10px]"></i> View
                                    </a>
                                    <a href="{{ route('families.edit', $family) }}"
                                       class="btn-secondary flex items-center gap-1 px-2.5 py-1 text-xs">
                                        <i class="fas fa-pen text-[10px]"></i> Edit
                                    </a>
                                    <form method="POST" action="{{ route('families.destroy', $family) }}"
                                          onsubmit="return confirm('Delete this family record?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn-secondary flex items-center gap-1 px-2.5 py-1 text-xs text-red-500 hover:text-red-600">
                                            <i class="fas fa-trash text-[10px]"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center">
                                <i class="fas fa-home mb-2 block text-3xl text-slate-200"></i>
                                <p class="text-sm text-slate-400">No families found.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-5">
            {{ $families->links() }}
        </div>
    </section>
</x-layouts.app>
