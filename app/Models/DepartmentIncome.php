<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepartmentIncome extends Model
{
    //
    protected $fillable = [
        'department',
        'income_type',
        'amount',
        'received_date',
        'attachment_url',
        'comment',
    ];
}
