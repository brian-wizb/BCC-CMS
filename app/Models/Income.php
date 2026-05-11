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
        'contributor_name',
        'contributor_contacts',
        'contributor_address',
        'attachment_url',
        'comment',
        'status',
    ];

    public function incomeType()
    {
        return $this->belongsTo(IncomeType::class);
    }

    public function member()
    {
        return $this->belongsTo(Member::class);
    }
}
