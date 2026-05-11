<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Donation extends Model
{
    protected $fillable = [
        'member_id', 'donor_name', 'donor_email',
        'type', 'tithe_code', 'amount', 'reference',
        'method', 'donation_date', 'campaign_id', 'notes', 'attachment',
    ];

    protected function casts(): array
    {
        return [
            'donation_date' => 'date',
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }
}
