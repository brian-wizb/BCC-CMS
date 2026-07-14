<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Communication extends Model
{
    use HasFactory;

    protected $fillable = [
        'channel',
        'audience_type',
        'subject',
        'message',
        'filters_json',
        'status',
        'estimated_sms_count',
        'actual_sms_count',
        'created_by',
        'sent_at',
    ];

    protected function casts(): array
    {
        return [
            'filters_json' => 'array',
            'sent_at' => 'datetime',
            'estimated_sms_count' => 'integer',
            'actual_sms_count' => 'integer',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(CommunicationDelivery::class);
    }
}
