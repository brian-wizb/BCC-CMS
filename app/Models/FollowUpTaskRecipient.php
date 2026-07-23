<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpTaskRecipient extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'person_type',
        'person_id',
        'display_name',
        'phone',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(FollowUpTask::class, 'task_id');
    }
}
