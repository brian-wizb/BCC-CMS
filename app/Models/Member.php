<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Member extends Model
{
    use HasFactory;

    protected static function booted(): void
    {
        static::creating(function (self $model) {
            if (empty($model->qr_token)) {
                $model->qr_token = Str::random(32);
            }
        });
    }

    protected $fillable = [
        'family_id',
        'full_name',
        'phone',
        'tithe_code',
        'gender',
        'zone',
        'residency',
        'marital_status',
        'profile_pic',
        'date_of_birth',
        'partner_name',
        'married_date',
        'is_born_again',
        'born_again_date',
        'is_baptized',
        'baptized_date',
        'holy_spirit_baptised',
        'membership_date',
        'member_code',
        'username',
        'email',
        'remarks',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'married_date' => 'date',
            'born_again_date' => 'date',
            'baptized_date' => 'date',
            'membership_date' => 'date',
            'is_born_again' => 'boolean',
            'is_baptized' => 'boolean',
            'holy_spirit_baptised' => 'boolean',
        ];
    }

    public function family(): BelongsTo
    {
        return $this->belongsTo(Family::class);
    }

    public function departmentMemberships(): HasMany
    {
        return $this->hasMany(DepartmentMember::class);
    }

    public function zoneMemberships(): HasMany
    {
        return $this->hasMany(ZoneMember::class);
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'department_members')
            ->withPivot(['role', 'status', 'joined_at'])
            ->withTimestamps();
    }

    public function zones(): BelongsToMany
    {
        return $this->belongsToMany(Zone::class, 'zone_members')
            ->withPivot(['status', 'joined_at'])
            ->withTimestamps();
    }

    public function timelineEvents(): HasMany
    {
        return $this->hasMany(MemberTimelineEvent::class)->latest('event_date');
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class)->latest('recorded_at');
    }

    public function prayerRequests(): HasMany
    {
        return $this->hasMany(PrayerRequest::class)->latest('created_at');
    }

    public function pastoralCases(): HasMany
    {
        return $this->hasMany(PastoralCase::class)->latest('opened_at');
    }

    public function followUpTasks(): HasMany
    {
        return $this->hasMany(FollowUpTask::class, 'person_id')
            ->where('person_type', 'member');
    }
}
