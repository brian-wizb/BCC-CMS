@props(['status', 'tone' => null])

@php
    $value = strtolower((string) $status);
    $resolvedTone = $tone;

    if ($resolvedTone === null) {
        $resolvedTone = match ($value) {
            'active', 'system admin', 'system_admin' => 'success',
            'inactive' => 'danger',
            default => 'info',
        };
    }

    $classes = match ($resolvedTone) {
        'success' => 'status-pill bg-[var(--color-success-100)] text-[var(--color-success-700)]',
        'danger' => 'status-pill bg-[var(--color-danger-100)] text-[var(--color-danger-700)]',
        'warning' => 'status-pill bg-[var(--color-warning-100)] text-[var(--color-warning-700)]',
        default => 'status-pill bg-[var(--color-brand-50)] text-[var(--color-brand-900)]',
    };
@endphp

<span {{ $attributes->class([$classes]) }}>
    {{ str_replace('_', ' ', (string) $status) }}
</span>
