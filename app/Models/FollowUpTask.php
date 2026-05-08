<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function history(): HasMany
    {
        return $this->hasMany(FollowUpHistory::class, 'task_id');
    }
}
