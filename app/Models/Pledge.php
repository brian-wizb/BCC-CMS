<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pledge extends Model
{
    protected $fillable = [
        'member_id', 'pledger_name', 'pledger_phone', 'pledger_email', 'pledge_type',
        'amount', 'pledge_date', 'due_date', 'campaign_id', 'notes',
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
