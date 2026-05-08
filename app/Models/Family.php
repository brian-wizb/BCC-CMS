<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Family extends Model
{
    use HasFactory;

    protected $fillable = [
        'head_of_family',
        'gender',
        'phone',
        'zone',
        'address',
        'home_cell_group',
        'joined_date',
        'remarks',
        'guest_members',
    ];

    protected function casts(): array
    {
        return [
            'joined_date'   => 'date',
            'guest_members' => 'array',
        ];
    }

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function pastoralCases(): HasMany
    {
        return $this->hasMany(PastoralCase::class)->latest('opened_at');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class)->latest('recorded_at');
    }

    /** Computed member count from real linked records */
    public function getMemberCountAttribute(): int
    {
        return $this->members()->count();
    }

    public function followUpTasks(): HasMany
    {
        return $this->hasMany(FollowUpTask::class, 'person_id')
            ->where('person_type', 'family');
    }
}
