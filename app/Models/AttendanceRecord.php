<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'member_id',
        'visitor_id',
        'department_id',
        'zone_id',
        'zone',
        'attendance_status',
        'attendance_mode',
        'check_in_time',
        'notes',
        'recorded_by',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'recorded_at'   => 'datetime',
            'check_in_time' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function visitor(): BelongsTo
    {
        return $this->belongsTo(Visitor::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class, 'zone_id');
    }

    public function recorder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    /** Resolved person name regardless of type */
    public function getPersonNameAttribute(): string
    {
        return $this->member?->full_name
            ?? $this->visitor?->full_name
            ?? 'Unknown';
    }

    /** Person type label */
    public function getPersonTypeAttribute(): string
    {
        if ($this->member_id)  { return 'member'; }
        if ($this->visitor_id) { return 'visitor'; }
        return 'unknown';
    }
}
