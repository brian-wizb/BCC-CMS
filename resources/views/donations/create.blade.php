<x-layouts.app title="Add Donation">

    <div class="mx-auto max-w-2xl space-y-6">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <span class="flex h-10 w-10 items-center justify-center rounded-xl" style="background:rgba(16,185,129,0.12);">
                    <i class="fas fa-hand-holding-heart text-base" style="color:rgba(16,185,129,0.9);"></i>
                </span>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-400">Finance &rsaquo; Donations</p>
                    <h3 class="text-xl font-semibold text-[var(--color-ink-950)]">Add Donation</h3>
                </div>
            </div>
            <a href="{{ route('donations.index') }}" class="btn-secondary flex items-center gap-1.5 text-sm">
                <i class="fas fa-arrow-left text-xs"></i> Back
            </a>
        </div>

        <article class="surface-card p-6">
            <form method="POST" action="{{ route('donations.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- Member --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Member</label>
                    <select name="member_id" id="member_id" class="form-input w-full" data-tom-select data-placeholder="— Anonymous / unlisted —">
                        <option value="">— Anonymous / unlisted —</option>
                        @foreach ($members as $m)
                            <option value="{{ $m->id }}" data-tithe-code="{{ $m->tithe_code }}" @selected(old('member_id') == $m->id)>
                                {{ $m->full_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('member_id')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Donation Type + Amount --}}
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Donation Type</label>
                        <select name="type" class="form-input w-full" required>
                            <option value="">Select</option>
                            @foreach (['Tithe [Zaka]', 'Sadaka ya Shukrani', 'Mission', 'Other'] as $t)
                                <option value="{{ $t }}" @selected(old('type') === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                        @error('type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Amount (TZS)</label>
                        <input type="number" name="amount" step="0.01" min="0" value="{{ old('amount') }}"
                               class="form-input w-full" placeholder="0" required>
                        @error('amount')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Tithe Code --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Tithe Code
                        <span class="ml-1 font-normal normal-case text-slate-400">(auto-filled from member)</span>
                    </label>
                    <input type="text" name="tithe_code" id="tithe_code" class="form-input w-full bg-slate-50"
                           value="{{ old('tithe_code') }}" readonly>
                    @error('tithe_code')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Reference + Payment Method + Date --}}
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Reference</label>
                        <input type="text" name="reference" class="form-input w-full" value="{{ old('reference') }}">
                        @error('reference')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Payment Method</label>
                        <select name="method" class="form-input w-full" required>
                            @foreach (['Cash', 'Mobile', 'Credit', 'Cheque', 'Bank'] as $m)
                                <option value="{{ $m }}" @selected(old('method', 'Cash') === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                        @error('method')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Date</label>
                        <input type="date" name="donation_date" class="form-input w-full"
                               value="{{ old('donation_date', today()->toDateString()) }}" required>
                        @error('donation_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Attachment --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Attachment <span class="font-normal normal-case text-slate-400">(bank slip, JPG/PNG/PDF, max 2 MB)</span>
                    </label>
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="form-input w-full">
                    @error('attachment')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Details / Notes</label>
                    <textarea name="notes" class="form-input w-full" rows="3" placeholder="if any">{{ old('notes') }}</textarea>
                    @error('notes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('donations.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary flex items-center gap-1.5"><i class="fas fa-save text-xs"></i> Save Donation</button>
                </div>
            </form>
        </article>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var sel = document.getElementById('member_id');
            if (sel && sel.tomselect) {
                sel.tomselect.on('change', function(value) {
                    var opt = sel.querySelector('option[value="' + value + '"]');
                    document.getElementById('tithe_code').value = opt ? (opt.dataset.titheCode || '') : '';
                });
            }
        });
    </script>
    @endpush

</x-layouts.app>
