<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'name', 'designation', 'phone', 'account_name', 'account_number',
    ];

    public function payrolls()
    {
        return $this->hasMany(Payroll::class);
    }
}
