<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUpHistory extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $table = 'follow_up_history';

    protected $fillable = [
        'task_id',
        'action_taken',
        'notes',
        'created_by',
    ];

    public function task(): BelongsTo
    {
        return $this->belongsTo(FollowUpTask::class, 'task_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
