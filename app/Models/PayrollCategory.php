<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollCategory extends Model
{
    protected $fillable = [
        'name', 'type', 'charge_in', 'charge', 'deduct_after_paye',
    ];
}
