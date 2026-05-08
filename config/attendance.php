<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Service Types
    |--------------------------------------------------------------------------
    | Canonical list of service types used for validation and form dropdowns.
    */
    'service_types' => [
        'sunday_first'    => 'Sunday First Service',
        'sunday_second'   => 'Sunday Second Service',
        'sunday_third'    => 'Sunday Third Service',
        'midweek'         => 'Midweek Service',
        'prayer_meeting'  => 'Prayer Meeting',
        'youth_service'   => 'Youth Service',
        'women_service'   => 'Women\'s Service',
        'men_service'     => 'Men\'s Service',
        'special_service' => 'Special Service',
        'conference'      => 'Conference',
        'other'           => 'Other',
    ],

    /*
    |--------------------------------------------------------------------------
    | Attendance Statuses
    |--------------------------------------------------------------------------
    */
    'statuses' => [
        'present' => 'Present',
        'late'    => 'Late',
        'excused' => 'Excused',
        'absent'  => 'Absent',
    ],

    /*
    |--------------------------------------------------------------------------
    | Attendance Modes
    |--------------------------------------------------------------------------
    */
    'modes' => [
        'in_person' => 'In Person',
        'online'    => 'Online',
        'hybrid'    => 'Hybrid',
    ],

    /*
    |--------------------------------------------------------------------------
    | Recurrence Rules
    |--------------------------------------------------------------------------
    */
    'recurrence_rules' => [
        'none'      => 'No Recurrence',
        'weekly'    => 'Weekly',
        'biweekly'  => 'Every 2 Weeks',
        'monthly'   => 'Monthly (1st occurrence)',
    ],

    /*
    |--------------------------------------------------------------------------
    | Absence Alert Threshold
    |--------------------------------------------------------------------------
    | Number of consecutive absences before a follow-up task is auto-created.
    */
    'absence_alert_threshold' => 3,

    /*
    |--------------------------------------------------------------------------
    | Grace Period (minutes)
    |--------------------------------------------------------------------------
    | Minutes after service start_time before a check-in is marked "late".
    */
    'late_grace_minutes' => 15,

];
