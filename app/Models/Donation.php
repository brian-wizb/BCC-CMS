<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    protected $fillable = [
        'donor_name', 'donor_email', 'amount', 'method', 'donation_date', 'campaign_id', 'notes',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
