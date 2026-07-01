@props(['status', 'tone' => null])

@php
    $value = strtolower((string) $status);
    $resolvedTone = $tone;

    if ($resolvedTone === null) {
        $resolvedTone = match ($value) {
            'open' => 'danger',
            'acknowledged' => 'warning',
            'resolved' => 'success',
            'active', 'system admin', 'system_admin' => 'success',
            'inactive' => 'danger',
            default => 'info',
        };
    }

    $classes = match ($resolvedTone) {
        'success' => 'status-pill bg-emerald-100 text-emerald-700 font-semibold',
        'danger' => 'status-pill bg-rose-100 text-rose-700 font-semibold',
        'warning' => 'status-pill bg-amber-100 text-amber-700 font-semibold',
        default => 'status-pill bg-[var(--color-brand-50)] text-[var(--color-brand-900)]',
    };
@endphp

<span {{ $attributes->class([$classes]) }}>
    {{ str_replace('_', ' ', (string) $status) }}
</span>
