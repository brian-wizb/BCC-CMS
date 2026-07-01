<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Leader extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->qr_token)) {
                $model->qr_token = Str::random(32);
            }
        });
    }

    protected $fillable = [
        'member_id',
        'user_id',
        'full_name',
        'phone',
        'email',
        'role',
        'zone',
        'status',
        'notes',
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function followUpTasks(): HasMany
    {
        return $this->hasMany(FollowUpTask::class, 'leader_id');
    }
}
