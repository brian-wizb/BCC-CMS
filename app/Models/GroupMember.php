<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GroupMember extends Model
{
    protected $fillable = [
        'group_id',
        'member_id',
        'guest_name',
        'guest_phone',
        'role',
        'joined_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'joined_at' => 'date',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    /** Display name — registered member name takes precedence over guest name. */
    public function getDisplayNameAttribute(): string
    {
        return $this->member?->full_name ?? $this->guest_name ?? '—';
    }

    /** Phone — registered member phone takes precedence. */
    public function getDisplayPhoneAttribute(): string
    {
        return $this->member?->phone ?? $this->guest_phone ?? '—';
    }
}
