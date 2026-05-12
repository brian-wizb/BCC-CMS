@props([
    'action',
    'departments' => collect(),
    'zones'       => collect(),
    'departmentId' => null,
    'zone'         => '',
    'dateFrom'     => null,
    'dateTo'       => null,
])

<div class="surface-card p-4 mt-4 print-hide">
    <form method="GET" action="{{ $action }}" class="flex flex-wrap items-end gap-3">
        <div class="flex flex-col gap-1 min-w-[160px]">
            <label class="text-xs font-medium text-slate-500">Department</label>
            <select name="department_id" class="form-input">
                <option value="">All departments</option>
                @foreach ($departments as $department)
                    <option value="{{ $department->id }}" @selected((string) $departmentId === (string) $department->id)>{{ $department->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1 min-w-[160px]">
            <label class="text-xs font-medium text-slate-500">Zone</label>
            <select name="zone" class="form-input">
                <option value="">All zones</option>
                @foreach ($zones as $zoneItem)
                    <option value="{{ $zoneItem->name }}" @selected((string) $zone === (string) $zoneItem->name)>{{ $zoneItem->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-slate-500">From</label>
            <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-input">
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium text-slate-500">To</label>
            <input type="date" name="date_to" value="{{ $dateTo }}" class="form-input">
        </div>
        <button type="submit" class="btn-secondary self-end">Apply</button>
        <a href="{{ $action }}" class="btn-secondary self-end">Clear</a>
    </form>
</div>
