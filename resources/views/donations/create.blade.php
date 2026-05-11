<x-layouts.app title="Add Donation">

    <div class="mx-auto max-w-2xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[var(--color-ink-950)]">Add Donation</h1>
            <a href="{{ route('donations.index') }}" class="btn-secondary text-sm">← All Records</a>
        </div>

        <article class="surface-card p-6">
            <form method="POST" action="{{ route('donations.store') }}" enctype="multipart/form-data" class="space-y-5">
                @csrf

                {{-- Member --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Member</label>
                    <select name="member_id" id="member_id" class="form-input w-full" onchange="fillTitheCode(this)">
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

                <div class="flex justify-end">
                    <button type="submit" class="btn-primary px-8">Save Donation</button>
                </div>
            </form>
        </article>
    </div>

    @push('scripts')
    <script>
        function fillTitheCode(select) {
            const opt = select.options[select.selectedIndex];
            document.getElementById('tithe_code').value = opt.dataset.titheCode || '';
        }
    </script>
    @endpush

</x-layouts.app>
