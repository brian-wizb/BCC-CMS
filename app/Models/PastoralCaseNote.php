<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PastoralCaseNote extends Model
{
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'case_id',
        'note',
        'visibility',
        'created_by',
    ];

    public function pastoralCase(): BelongsTo
    {
        return $this->belongsTo(PastoralCase::class, 'case_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
