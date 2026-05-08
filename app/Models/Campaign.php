<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'name', 'description', 'start_date', 'end_date', 'target_amount',
    ];

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }
    public function pledges()
    {
        return $this->hasMany(Pledge::class);
    }
}
