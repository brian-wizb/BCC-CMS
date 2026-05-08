<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pledge extends Model
{
    protected $fillable = [
        'pledger_name', 'pledger_email', 'amount', 'pledge_date', 'campaign_id', 'notes',
    ];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
    public function payments()
    {
        return $this->hasMany(PledgePayment::class);
    }
    public function missedPledges()
    {
        return $this->hasMany(MissedPledge::class);
    }
}
