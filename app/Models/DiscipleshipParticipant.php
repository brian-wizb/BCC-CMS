<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscipleshipParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id', 'external_name', 'external_phone', 'external_email', 'remarks',
        'certificate_number', 'certificate_awarded_at',
    ];

    protected function casts(): array
    {
        return ['certificate_awarded_at' => 'datetime'];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function stages(): HasMany
    {
        return $this->hasMany(DiscipleshipStageProgress::class);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->member?->full_name ?: ($this->external_name ?: 'Unnamed participant');
    }

    public function getHasCompletedFoundationAttribute(): bool
    {
        return $this->stages->where('status', 'completed')->count() === 4;
    }
}
