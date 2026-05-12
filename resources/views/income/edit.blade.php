<x-layouts.app title="Edit Income">
    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(16,185,129,0.12);">
                    <i class="fas fa-pen text-base" style="color:rgba(16,185,129,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance &rsaquo; Income</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Edit Income</h3>
                </div>
            </div>
            <a href="{{ route('income.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                <i class="fas fa-arrow-left text-xs"></i> All Income
            </a>
        </div>

        <article class="surface-card p-6">
            <form method="POST" action="{{ route('income.update', $income) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf @method('PUT')
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-bookmark mr-1 opacity-60"></i>Income Type <span class="text-rose-500">*</span>
                        </label>
                        <select name="income_type_id" class="form-input w-full" required>
                            <option value="">— Select type —</option>
                            @foreach($incomeTypes as $type)
                                <option value="{{ $type->id }}" @selected(old('income_type_id', $income->income_type_id) == $type->id)>{{ $type->type }}</option>
                            @endforeach
                        </select>
                        @error('income_type_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-info-circle mr-1 opacity-60"></i>Status
                        </label>
                        <select name="status" class="form-input w-full">
                            <option value="Received" @selected(old('status', $income->status) === 'Received')>Received</option>
                            <option value="Pending" @selected(old('status', $income->status) === 'Pending')>Pending</option>
                        </select>
                    </div>
                </div>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-coins mr-1 opacity-60"></i>Amount (Tsh.) <span class="text-rose-500">*</span>
                        </label>
                        <input type="number" step="1" min="0" name="amount" class="form-input w-full"
                            value="{{ old('amount', $income->amount) }}" placeholder="0" required>
                        @error('amount')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-calendar-alt mr-1 opacity-60"></i>Received Date <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="received_date" class="form-input w-full"
                            value="{{ old('received_date', $income->received_date) }}" required>
                        @error('received_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Contributor Section --}}
                <div class="rounded-xl border border-[var(--color-surface-200)] p-4">
                    <p class="mb-3 text-xs font-semibold uppercase tracking-wide text-slate-400">Contributor (Optional)</p>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-user-circle mr-1 opacity-60"></i>Member
                            </label>
                            <select name="member_id" class="form-input w-full" data-tom-select data-placeholder="— Search member —">
                                <option value="">— Search member —</option>
                                @foreach($members as $m)
                                    <option value="{{ $m->id }}" @selected(old('member_id', $income->member_id) == $m->id)>
                                        {{ $m->full_name ?? $m->name ?? '#'.$m->id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <i class="fas fa-user mr-1 opacity-60"></i>Contributor Name
                                </label>
                                <input type="text" name="contributor_name" class="form-input w-full"
                                    value="{{ old('contributor_name', $income->contributor_name) }}" placeholder="Full name">
                            </div>
                            <div>
                                <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    <i class="fas fa-phone mr-1 opacity-60"></i>Contacts
                                </label>
                                <input type="text" name="contributor_contacts" class="form-input w-full"
                                    value="{{ old('contributor_contacts', $income->contributor_contacts) }}" placeholder="Phone or email">
                            </div>
                        </div>
                        <div>
                            <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                                <i class="fas fa-map-marker-alt mr-1 opacity-60"></i>Address
                            </label>
                            <input type="text" name="contributor_address" class="form-input w-full"
                                value="{{ old('contributor_address', $income->contributor_address) }}" placeholder="City / District">
                        </div>
                    </div>
                </div>

                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-paperclip mr-1 opacity-60"></i>Attachment
                        </label>
                        <input type="file" name="attachment" class="form-input w-full">
                        @if($income->attachment_url)
                            <p class="mt-1 text-xs text-slate-400"><a href="{{ $income->attachment_url }}" target="_blank" class="text-blue-600 underline">Current file</a> (upload new to replace)</p>
                        @endif
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                            <i class="fas fa-align-left mr-1 opacity-60"></i>Comment
                        </label>
                        <textarea name="comment" rows="2" class="form-input w-full" placeholder="Optional notes...">{{ old('comment', $income->comment) }}</textarea>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="submit" class="btn-primary flex items-center gap-1.5">
                        <i class="fas fa-save text-xs"></i> Update Income
                    </button>
                    <a href="{{ route('income.index') }}" class="btn-secondary text-sm">Cancel</a>
                </div>
            </form>
        </article>
    </div>
</x-layouts.app>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0"><i class="bi bi-pencil-square text-warning me-2"></i>Edit Income Record</h3>
        <a href="{{ route('income.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Income Records
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <div class="row">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('income.update', $income) }}" enctype="multipart/form-data">
                        @csrf @method('PUT')
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-bookmark me-1"></i>Income Type</label>
                            <select name="income_type_id" class="form-select @error('income_type_id') is-invalid @enderror" required>
                                <option value="">— Select type —</option>
                                @foreach($incomeTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('income_type_id', $income->income_type_id)==$type->id?'selected':'' }}>{{ $type->type }}</option>
                                @endforeach
                            </select>
                            @error('income_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-currency-exchange me-1"></i>Amount</label>
                                <div class="input-group">
                                    <span class="input-group-text">Tsh.</span>
                                    <input type="number" step="1" min="0" name="amount" class="form-control"
                                        value="{{ old('amount', $income->amount) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold"><i class="bi bi-calendar me-1"></i>Received Date</label>
                                <input type="date" name="received_date" class="form-control"
                                    value="{{ old('received_date', $income->received_date) }}" required>
                            </div>
                        </div>

                        <div class="card border bg-light mb-3">
                            <div class="card-header bg-transparent py-2">
                                <h6 class="mb-0 fw-semibold"><i class="bi bi-person-circle me-2 text-success"></i>Contributor Information</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label fw-semibold small">Existing Member (optional)</label>
                                    <select name="member_id" class="form-select form-select-sm">
                                        <option value="">— Search existing member —</option>
                                        @foreach($members as $member)
                                            <option value="{{ $member->id }}" {{ old('member_id', $income->member_id)==$member->id?'selected':'' }}>
                                                {{ $member->full_name ?? $member->name ?? $member->first_name.' '.$member->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-semibold small">Contributor Name</label>
                                        <input type="text" name="contributor_name" class="form-control form-control-sm"
                                            value="{{ old('contributor_name', $income->contributor_name) }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label fw-semibold small">Contacts</label>
                                        <input type="text" name="contributor_contacts" class="form-control form-control-sm"
                                            value="{{ old('contributor_contacts', $income->contributor_contacts) }}">
                                    </div>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label fw-semibold small">Address</label>
                                    <input type="text" name="contributor_address" class="form-control form-control-sm"
                                        value="{{ old('contributor_address', $income->contributor_address) }}">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-paperclip me-1"></i>Attachment</label>
                            @if($income->attachment_url)
                                <div class="mb-1">
                                    <a href="{{ $income->attachment_url }}" target="_blank" class="text-primary small">
                                        <i class="bi bi-file-earmark me-1"></i>View current attachment
                                    </a>
                                </div>
                            @endif
                            <input type="file" name="attachment" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold"><i class="bi bi-chat-left-text me-1"></i>Comment</label>
                            <textarea name="comment" class="form-control" rows="3">{{ old('comment', $income->comment) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Status</label>
                            <select name="status" class="form-select">
                                <option value="Received" {{ old('status', $income->status)=='Received'?'selected':'' }}>Received</option>
                                <option value="Pending" {{ old('status', $income->status)=='Pending'?'selected':'' }}>Pending</option>
                            </select>
                        </div>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-warning px-4">
                                <i class="bi bi-save me-1"></i>Update
                            </button>
                            <a href="{{ route('income.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
