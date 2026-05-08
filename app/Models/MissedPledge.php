<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MissedPledge extends Model
{
    protected $fillable = [
        'pledge_id', 'missed_date', 'reason',
    ];

    public function pledge()
    {
        return $this->belongsTo(Pledge::class);
    }
}
