<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $fillable = [
        'income_type_id',
        'amount',
        'received_date',
        'member_id',
        'attachment_url',
        'comment',
    ];

    public function incomeType()
    {
        return $this->belongsTo(IncomeType::class);
    }
}
