@props([
    'action',
    'departments' => collect(),
    'zones' => collect(),
    'departmentId' => null,
    'zone' => '',
])

<form method="GET" action="{{ $action }}" class="mt-4 grid gap-3 md:grid-cols-[minmax(0,1fr)_minmax(0,1fr)_auto_auto]">
    <select name="department_id" class="form-input">
        <option value="">All departments</option>
        @foreach ($departments as $department)
            <option value="{{ $department->id }}" @selected((string) $departmentId === (string) $department->id)>{{ $department->name }}</option>
        @endforeach
    </select>

    <select name="zone" class="form-input">
        <option value="">All zones</option>
        @foreach ($zones as $zoneItem)
            <option value="{{ $zoneItem->name }}" @selected((string) $zone === (string) $zoneItem->name)>{{ $zoneItem->name }}</option>
        @endforeach
    </select>

    <button type="submit" class="btn-secondary">Apply filters</button>
    <a href="{{ $action }}" class="btn-secondary">Clear</a>
</form>
