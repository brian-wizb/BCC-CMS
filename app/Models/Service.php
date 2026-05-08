<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'service_type',
        'service_date',
        'start_time',
        'end_time',
        'location',
        'description',
        'recurrence_rule',
        'attendance_mode',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
        ];
    }

    public function visitors(): HasMany
    {
        return $this->hasMany(Visitor::class);
    }

    public function attendanceRecords(): HasMany
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    /** Generate next occurrence date based on recurrence_rule */
    public function nextOccurrenceDate(): ?\Carbon\Carbon
    {
        return match ($this->recurrence_rule) {
            'weekly'   => $this->service_date->addWeek(),
            'biweekly' => $this->service_date->addWeeks(2),
            'monthly'  => $this->service_date->addMonth(),
            default    => null,
        };
    }
}
