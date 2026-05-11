<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentExpense extends Model
{
    protected $fillable = [
        'department',
        'expense',
        'payment_method',
        'amount',
        'expense_date',
        'reference_no',
        'comment',
        'attachment_url',
    ];
}
