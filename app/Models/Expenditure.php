<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expenditure extends Model
{
    protected $fillable = [
        'expense_category',
        'payment_method',
        'amount',
        'expense_date',
        'reference_no',
        'comment',
        'attachment_url',
        'status',
    ];
}
