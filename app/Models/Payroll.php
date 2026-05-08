<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $fillable = [
        'employee_id', 'payment_date', 'method', 'account_name', 'account_number',
        'salary', 'tax_percent', 'church_staffs_addition', 'paye', 'other_amount',
        'net_salary', 'take_home', 'paid_amount', 'details', 'attachment_url',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
