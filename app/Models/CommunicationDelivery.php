<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommunicationDelivery extends Model
{
    use HasFactory;

    protected $fillable = [
        'communication_id',
        'recipient_type',
        'recipient_id',
        'recipient_contact',
        'delivery_status',
        'provider_response',
        'delivered_at',
    ];

    protected function casts(): array
    {
        return [
            'delivered_at' => 'datetime',
        ];
    }

    public function communication(): BelongsTo
    {
        return $this->belongsTo(Communication::class);
    }
}
