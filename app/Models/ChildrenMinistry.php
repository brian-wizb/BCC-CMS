<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChildrenMinistry extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'middle_name',
        'surname',
        'date_of_birth',
        'sex',
        'parent_name',
        'parent_contact',
        'parent_member_id',
        'remarks',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    /**
     * Get the full name of the child
     */
    public function getFullNameAttribute(): string
    {
        return collect([
            $this->first_name,
            $this->middle_name,
            $this->surname,
        ])->filter()->implode(' ');
    }

    /**
     * Get the parent member if linked
     */
    public function parentMember(): BelongsTo
    {
        return $this->belongsTo(Member::class, 'parent_member_id');
    }
}
