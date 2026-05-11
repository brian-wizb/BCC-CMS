<x-layouts.app title="Edit Donation">

    <div class="mx-auto max-w-2xl">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-2xl font-bold text-[var(--color-ink-950)]">Edit Donation</h1>
            <a href="{{ route('donations.index') }}" class="btn-secondary text-sm">← All Records</a>
        </div>

        <article class="surface-card p-6">
            <form method="POST" action="{{ route('donations.update', $donation) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PUT')

                {{-- Member --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Member</label>
                    <select name="member_id" id="member_id" class="form-input w-full" onchange="fillTitheCode(this)">
                        <option value="">— Anonymous / unlisted —</option>
                        @foreach ($members as $m)
                            <option value="{{ $m->id }}"
                                    data-tithe-code="{{ $m->tithe_code }}"
                                    @selected(old('member_id', $donation->member_id) == $m->id)>
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
                                <option value="{{ $t }}" @selected(old('type', $donation->type) === $t)>{{ $t }}</option>
                            @endforeach
                        </select>
                        @error('type')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Amount (TZS)</label>
                        <input type="number" name="amount" step="0.01" min="0"
                               value="{{ old('amount', $donation->amount) }}"
                               class="form-input w-full" required>
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
                           value="{{ old('tithe_code', $donation->tithe_code) }}" readonly>
                    @error('tithe_code')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Reference + Payment Method + Date --}}
                <div class="grid gap-4 sm:grid-cols-3">
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Reference</label>
                        <input type="text" name="reference" class="form-input w-full"
                               value="{{ old('reference', $donation->reference) }}">
                        @error('reference')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Payment Method</label>
                        <select name="method" class="form-input w-full" required>
                            @foreach (['Cash', 'Mobile', 'Credit', 'Cheque', 'Bank'] as $m)
                                <option value="{{ $m }}" @selected(old('method', $donation->method) === $m)>{{ $m }}</option>
                            @endforeach
                        </select>
                        @error('method')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Date</label>
                        <input type="date" name="donation_date" class="form-input w-full"
                               value="{{ old('donation_date', $donation->donation_date->toDateString()) }}" required>
                        @error('donation_date')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                {{-- Attachment --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">
                        Attachment <span class="font-normal normal-case text-slate-400">(bank slip, JPG/PNG/PDF, max 2 MB)</span>
                    </label>
                    @if ($donation->attachment)
                        <div class="mb-2 flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm">
                            <i class="fa-solid fa-paperclip text-slate-400"></i>
                            <a href="{{ Storage::url($donation->attachment) }}" target="_blank"
                               class="text-blue-600 underline">Current attachment</a>
                            <span class="text-slate-400">— upload a new file below to replace it.</span>
                        </div>
                    @endif
                    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.pdf" class="form-input w-full">
                    @error('attachment')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label class="mb-1 block text-xs font-semibold uppercase tracking-wide text-slate-500">Details / Notes</label>
                    <textarea name="notes" class="form-input w-full" rows="3">{{ old('notes', $donation->notes) }}</textarea>
                    @error('notes')<p class="mt-1 text-xs text-rose-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('donations.index') }}" class="btn-secondary">Cancel</a>
                    <button type="submit" class="btn-primary px-8">Update Donation</button>
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
