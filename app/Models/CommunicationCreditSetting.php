<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommunicationCreditSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'credits_purchased_total',
        'low_balance_threshold',
        'updated_by',
    ];
}
