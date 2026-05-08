<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PledgePayment extends Model
{
    protected $fillable = [
        'pledge_id', 'campaign_id', 'phone', 'invoice_number', 'amount', 'payment_date', 'method', 'attachment', 'notes',
    ];

    public function pledge()
    {
        return $this->belongsTo(Pledge::class);
    }
}
