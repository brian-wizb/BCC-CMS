<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class University extends Model
{
    protected $fillable = ['name', 'country', 'type'];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }
}
