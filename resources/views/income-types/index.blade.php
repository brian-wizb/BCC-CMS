<x-layouts.app title="Income Types">
    <div class="space-y-6">
        <div class="flex items-center gap-3">
            <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(139,92,246,0.12);">
                <i class="fas fa-tags text-base" style="color:rgba(139,92,246,0.9);"></i>
            </span>
            <div>
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance</p>
                <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Income Types</h3>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- List --}}
            <div class="lg:col-span-2">
                <article class="surface-card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-[var(--color-surface-200)] text-sm">
                            <thead class="bg-[var(--color-surface-50)] text-left text-xs font-semibold uppercase tracking-wide text-slate-400">
                                <tr>
                                    <th class="px-5 py-3">#</th>
                                    <th class="px-5 py-3">Type</th>
                                    <th class="px-5 py-3">Description</th>
                                    <th class="px-5 py-3">Incomes</th>
                                    <th class="px-5 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-[var(--color-surface-200)] bg-white">
                                @forelse($types as $type)
                                <tr class="hover:bg-slate-50 transition">
                                    <td class="px-5 py-3.5 text-slate-400">{{ $loop->iteration }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold" style="background:rgba(139,92,246,0.1); color:rgba(139,92,246,0.9);">
                                            <i class="fas fa-bookmark text-[10px]"></i>{{ $type->type }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-500">{{ $type->description ?: '—' }}</td>
                                    <td class="px-5 py-3.5">
                                        <span class="inline-block rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">{{ $type->incomes_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-5 py-3.5 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button"
                                                class="rounded px-2 py-1 text-xs font-medium text-blue-600 hover:bg-blue-50 btn-edit-type"
                                                data-id="{{ $type->id }}"
                                                data-type="{{ $type->type }}"
                                                data-description="{{ $type->description }}">
                                                <i class="fas fa-pen mr-1 text-[10px]"></i>Edit
                                            </button>
                                            <form method="POST" action="{{ route('income-types.destroy', $type) }}"
                                                onsubmit="return confirm('Delete {{ addslashes($type->type) }}?')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="rounded px-2 py-1 text-xs font-medium text-rose-600 hover:bg-rose-50">
                                                    <i class="fas fa-trash mr-1 text-[10px]"></i>Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="px-5 py-12 text-center text-slate-400">
                                        <i class="fas fa-tags mb-2 block text-2xl text-slate-300"></i>
                                        No income types yet. Add one on the right.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </article>
            </div>

            {{-- Add / Edit form --}}
            <div>
                <article class="surface-card p-6" id="type-form-card">
                    <h4 class="mb-4 text-sm font-semibold text-[var(--color-ink-950)]" id="form-heading">
                        <i class="fas fa-plus-circle mr-2 text-violet-500"></i>Add Income Type
                    </h4>

                    {{-- Add form --}}
                    <form id="add-form" action="{{ route('income-types.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Type Name</label>
                            <input type="text" name="type" class="form-input w-full" value="{{ old('type') }}"
                                placeholder="e.g. Tithe" required>
                            @error('type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Description</label>
                            <textarea name="description" rows="3" class="form-input w-full"
                                placeholder="Optional description...">{{ old('description') }}</textarea>
                        </div>
                        <button type="submit" class="btn-primary w-full flex items-center justify-center gap-1.5">
                            <i class="fas fa-save text-xs"></i> Save Type
                        </button>
                    </form>

                    {{-- Edit form (hidden by default) --}}
                    <form id="edit-form" method="POST" class="hidden space-y-4">
                        @csrf @method('PUT')
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Type Name</label>
                            <input type="text" id="edit-type" name="type" class="form-input w-full" required>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Description</label>
                            <textarea id="edit-description" name="description" rows="3" class="form-input w-full"></textarea>
                        </div>
                        <div class="flex gap-2">
                            <button type="submit" class="btn-primary flex flex-1 items-center justify-center gap-1.5">
                                <i class="fas fa-save text-xs"></i> Update
                            </button>
                            <button type="button" id="cancel-edit" class="btn-secondary text-sm">Cancel</button>
                        </div>
                    </form>
                </article>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
    document.querySelectorAll('.btn-edit-type').forEach(btn => {
        btn.addEventListener('click', () => {
            document.getElementById('edit-type').value = btn.dataset.type;
            document.getElementById('edit-description').value = btn.dataset.description || '';
            document.getElementById('edit-form').action = '/income-types/' + btn.dataset.id;
            document.getElementById('add-form').classList.add('hidden');
            document.getElementById('edit-form').classList.remove('hidden');
            document.getElementById('form-heading').innerHTML = '<i class="fas fa-pen mr-2 text-amber-500"></i>Edit Income Type';
            document.getElementById('type-form-card').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        });
    });
    document.getElementById('cancel-edit')?.addEventListener('click', () => {
        document.getElementById('edit-form').classList.add('hidden');
        document.getElementById('add-form').classList.remove('hidden');
        document.getElementById('form-heading').innerHTML = '<i class="fas fa-plus-circle mr-2 text-violet-500"></i>Add Income Type';
    });
    </script>
    @endpush
</x-layouts.app>