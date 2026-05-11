<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PastoralCase extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'family_id',
        'case_type',
        'priority',
        'status',
        'assigned_to',
        'opened_at',
        'closed_at',
        'summary',
    ];

    protected function casts(): array
    {
        return [
            'opened_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(Leader::class, 'assigned_to');
    }

    public function notes(): HasMany
    {
        return $this->hasMany(PastoralCaseNote::class, 'case_id')->latest('created_at');
    }
}
