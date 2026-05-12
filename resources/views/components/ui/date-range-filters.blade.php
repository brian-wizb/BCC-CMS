@props([
    'action',
    'dateFrom' => null,
    'dateTo'   => null,
])

<div class="surface-card p-4 print-hide">
    <form method="GET" action="{{ $action }}" class="flex flex-wrap items-end gap-3">
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-slate-500">From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-input">
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-slate-500">To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="form-input">
        </div>
        <button type="submit" class="btn-secondary self-end">Apply</button>
        @if($dateFrom || $dateTo)
            <a href="{{ $action }}" class="btn-secondary self-end">Clear</a>
            <p class="self-end text-xs text-slate-500">
                <i class="fas fa-filter mr-1 opacity-60"></i>
                @if($dateFrom) from {{ \Carbon\Carbon::parse($dateFrom)->format('d M Y') }} @endif
                @if($dateTo) to {{ \Carbon\Carbon::parse($dateTo)->format('d M Y') }} @endif
            </p>
        @endif
    </form>
</div>
