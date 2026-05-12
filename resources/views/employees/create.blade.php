<x-layouts.app title="Add Employee">
    <div class="mx-auto max-w-xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(99,102,241,0.12);">
                    <i class="fas fa-user-plus text-base" style="color:rgba(99,102,241,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance &rsaquo; Employees</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Add Employee</h3>
                </div>
            </div>
            <a href="{{ route('employees.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                <i class="fas fa-arrow-left text-xs"></i> All Employees
            </a>
        </div>

        @if($errors->any())
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
            <ul class="list-disc list-inside space-y-0.5">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
        @endif

        <article class="surface-card p-6">
            <form action="{{ route('employees.store') }}" method="POST" class="space-y-5">
                @csrf
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-user mr-1 opacity-60"></i>Name <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="name" class="form-input w-full" value="{{ old('name') }}" required>
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-briefcase mr-1 opacity-60"></i>Designation
                        </label>
                        <input type="text" name="designation" class="form-input w-full" value="{{ old('designation') }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-phone mr-1 opacity-60"></i>Phone
                        </label>
                        <input type="text" name="phone" class="form-input w-full" value="{{ old('phone') }}">
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-university mr-1 opacity-60"></i>Account Name
                        </label>
                        <input type="text" name="account_name" class="form-input w-full" value="{{ old('account_name') }}">
                    </div>
                    <div class="sm:col-span-2">
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-hashtag mr-1 opacity-60"></i>Account Number
                        </label>
                        <input type="text" name="account_number" class="form-input w-full" value="{{ old('account_number') }}">
                    </div>
                </div>
                <div class="flex justify-end gap-2">
                    <a href="{{ route('employees.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary"><i class="fas fa-save mr-1.5"></i>Save Employee</button>
                </div>
            </form>
        </article>
    </div>
</x-layouts.app>
