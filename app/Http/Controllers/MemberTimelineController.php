<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class MemberTimelineController extends Controller
{
    public function show(Member $member): View
    {
        $events = collect();

        if ($member->membership_date) {
            $events->push([
                'event_type' => 'membership',
                'event_date' => $member->membership_date,
                'title' => 'Membership Date',
                'details' => 'Member joined the church.',
            ]);
        }

        $attendanceEvents = $member->attendanceRecords()
            ->with('service:id,name,service_date')
            ->limit(50)
            ->get()
            ->map(function ($attendance) {
                $serviceLabel = $attendance->service?->name
                    ? 'Service: '.$attendance->service->name
                    : 'Service ID: '.$attendance->service_id;

                return [
                    'event_type' => 'attendance',
                    'event_date' => $attendance->recorded_at,
                    'title' => 'Attendance: '.str_replace('_', ' ', ucfirst($attendance->attendance_status)),
                    'details' => $serviceLabel,
                ];
            });

        $prayerEvents = $member->prayerRequests()
            ->limit(50)
            ->get()
            ->map(fn ($request) => [
                'event_type' => 'prayer_request',
                'event_date' => $request->created_at,
                'title' => 'Prayer Request: '.$request->request_type,
                'details' => $request->request_text,
            ]);

        $pastoralEvents = $member->pastoralCases()
            ->limit(50)
            ->get()
            ->map(fn ($caseItem) => [
                'event_type' => 'pastoral_case',
                'event_date' => $caseItem->opened_at,
                'title' => 'Pastoral Case: '.$caseItem->case_type,
                'details' => $caseItem->summary ?: 'No summary provided.',
            ]);

        $customEvents = $member->timelineEvents()
            ->limit(100)
            ->get()
            ->map(fn ($event) => [
                'event_type' => $event->event_type,
                'event_date' => $event->event_date,
                'title' => $event->title,
                'details' => $event->details,
            ]);

        $events = $events
            ->merge($attendanceEvents)
            ->merge($prayerEvents)
            ->merge($pastoralEvents)
            ->merge($customEvents)
            ->filter(fn (array $event) => $event['event_date'] !== null)
            ->sortByDesc(fn (array $event) => $event['event_date']->getTimestamp())
            ->values();

        return view('members.timeline', [
            'member' => $member,
            'events' => $events,
        ]);
    }
}
