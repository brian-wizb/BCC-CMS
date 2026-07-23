<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class FollowUpTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_type',
        'person_id',
        'leader_id',
        'assigned_to',
        'task_type',
        'priority',
        'due_date',
        'status',
        'notes',
        'completed_at',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function leader(): BelongsTo
    {
        return $this->belongsTo(Leader::class, 'leader_id');
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(FollowUpTaskRecipient::class, 'task_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(FollowUpHistory::class, 'task_id');
    }

    public function getTaskTypeLabelAttribute(): string
    {
        return match ($this->task_type) {
            'sms' => 'SMS',
            'zone_assignment' => 'Zone Assignment',
            default => Str::of((string) $this->task_type)->replace('_', ' ')->title()->toString(),
        };
    }

    public function getPersonDisplayTypeAttribute(): string
    {
        if ($this->recipients->count() > 1) {
            return 'Multiple people';
        }

        return Str::of((string) $this->person_type)->replace('_', ' ')->title()->toString();
    }

    public function getPersonDisplayNameAttribute(): string
    {
        if ($this->recipients->count() > 1) {
            return $this->recipientSummary;
        }

        if ($this->recipients->count() === 1) {
            return $this->recipients->first()?->display_name ?? 'Unknown';
        }

        return match ($this->person_type) {
            'member' => Member::query()->whereKey($this->person_id)->value('full_name') ?? 'Member #' . $this->person_id,
            'visitor' => Visitor::query()->whereKey($this->person_id)->value('full_name') ?? 'Visitor #' . $this->person_id,
            default => 'Unknown',
        };
    }

    public function getPersonPhoneAttribute(): ?string
    {
        if ($this->recipients->count() !== 1) {
            return null;
        }

        $recipient = $this->recipients->first();

        if ($recipient?->phone !== null && $recipient->phone !== '') {
            return $recipient->phone;
        }

        return match ($this->person_type) {
            'member' => Member::query()->whereKey($this->person_id)->value('phone'),
            'visitor' => Visitor::query()->whereKey($this->person_id)->value('phone'),
            default => null,
        };
    }

    public function getTargetDisplayTypeAttribute(): string
    {
        return $this->person_display_type;
    }

    public function getTargetDisplayNameAttribute(): string
    {
        return $this->person_display_name;
    }

    public function getTargetPhoneAttribute(): ?string
    {
        if ($this->recipients->count() > 1) {
            return $this->recipientPhoneSummary;
        }

        return $this->person_phone;
    }

    public function getTargetMemberCountAttribute(): int
    {
        return $this->recipients->count();
    }

    public function getRecipientSummaryAttribute(): string
    {
        $names = $this->recipients->pluck('display_name')->filter()->values();

        if ($names->isEmpty()) {
            return 'Multiple people';
        }

        $preview = $names->take(3)->all();
        $remaining = max(0, $names->count() - count($preview));

        return $remaining > 0
            ? implode(', ', $preview) . ' +' . $remaining . ' more'
            : implode(', ', $preview);
    }

    public function getRecipientPhoneSummaryAttribute(): ?string
    {
        $phones = $this->recipients
            ->pluck('phone')
            ->filter(fn ($phone) => filled($phone))
            ->unique()
            ->values();

        if ($phones->isEmpty()) {
            return null;
        }

        $preview = $phones->take(3)->all();
        $remaining = max(0, $phones->count() - count($preview));

        return $remaining > 0
            ? implode(', ', $preview) . ' +' . $remaining . ' more'
            : implode(', ', $preview);
    }

    public static function stripSystemNotes(?string $notes): string
    {
        $cleaned = trim((string) $notes);

        if ($cleaned === '') {
            return '';
        }

        $cleaned = preg_replace('/^\s*(ALERT_REF:\d+|From alert #\d+:.*)\s*$/mi', '', $cleaned) ?? $cleaned;
        $cleaned = preg_replace('/\R{3,}/', PHP_EOL . PHP_EOL, $cleaned) ?? $cleaned;

        return trim($cleaned);
    }

    public function getDisplayNotesAttribute(): ?string
    {
        $cleaned = static::stripSystemNotes($this->notes);

        return $cleaned !== '' ? $cleaned : null;
    }

    public function getSystemNotesAttribute(): ?string
    {
        preg_match_all('/^\s*(ALERT_REF:\d+|From alert #\d+:.*)\s*$/mi', (string) $this->notes, $matches);

        $metadata = array_values(array_filter(array_map('trim', $matches[0] ?? [])));

        return $metadata !== [] ? implode(PHP_EOL, $metadata) : null;
    }

    public function mergeDisplayNotes(?string $notes): ?string
    {
        $parts = array_values(array_filter([
            trim((string) $notes) ?: null,
            $this->system_notes,
        ]));

        return $parts !== [] ? implode(PHP_EOL, $parts) : null;
    }
}
