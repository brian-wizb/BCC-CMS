<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TagSampleDataSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();
        $userId = $this->ensureSystemUser();

        if (DB::table('members')->count() === 0) {
            DB::table('members')->insert([
                ['full_name' => 'Emmanuel Mwakalinga', 'phone' => '0712001001', 'tithe_code' => 'TAG-001', 'gender' => 'male', 'zone' => 'Mbezi', 'residency' => 'Dar es Salaam', 'marital_status' => 'married', 'date_of_birth' => '1983-03-12', 'membership_date' => '2017-01-08', 'member_code' => 'MEM-001', 'remarks' => 'Cell leader', 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Neema Kaseke', 'phone' => '0712001002', 'tithe_code' => 'TAG-002', 'gender' => 'female', 'zone' => 'Mbezi', 'residency' => 'Dar es Salaam', 'marital_status' => 'married', 'date_of_birth' => '1990-07-19', 'membership_date' => '2018-06-14', 'member_code' => 'MEM-002', 'remarks' => 'Choir ministry', 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Rehema Msuya', 'phone' => '0712001003', 'tithe_code' => 'TAG-003', 'gender' => 'female', 'zone' => 'Kimara', 'residency' => 'Dar es Salaam', 'marital_status' => 'single', 'date_of_birth' => '1998-11-08', 'membership_date' => '2020-02-02', 'member_code' => 'MEM-003', 'remarks' => 'Youth coordinator', 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Yohana Mnzava', 'phone' => '0712001004', 'tithe_code' => 'TAG-004', 'gender' => 'male', 'zone' => 'Kimara', 'residency' => 'Dar es Salaam', 'marital_status' => 'single', 'date_of_birth' => '2002-05-16', 'membership_date' => '2021-08-07', 'member_code' => 'MEM-004', 'remarks' => 'Media volunteer', 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Asha Mwajuma', 'phone' => '0712001005', 'tithe_code' => 'TAG-005', 'gender' => 'female', 'zone' => 'Tegeta', 'residency' => 'Dar es Salaam', 'marital_status' => 'married', 'date_of_birth' => '1976-09-01', 'membership_date' => '2016-04-10', 'member_code' => 'MEM-005', 'remarks' => 'Women ministry', 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Josephat Mrema', 'phone' => '0712001006', 'tithe_code' => 'TAG-006', 'gender' => 'male', 'zone' => 'Tegeta', 'residency' => 'Dar es Salaam', 'marital_status' => 'married', 'date_of_birth' => '1972-12-24', 'membership_date' => '2015-11-12', 'member_code' => 'MEM-006', 'remarks' => 'Elder', 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Tumaini Mbwambo', 'phone' => '0712001007', 'tithe_code' => 'TAG-007', 'gender' => 'female', 'zone' => 'Sinza', 'residency' => 'Dar es Salaam', 'marital_status' => 'single', 'date_of_birth' => '2006-04-03', 'membership_date' => '2024-01-21', 'member_code' => 'MEM-007', 'remarks' => 'Teen fellowship', 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Baraka Chacha', 'phone' => '0712001008', 'tithe_code' => 'TAG-008', 'gender' => 'male', 'zone' => 'Sinza', 'residency' => 'Dar es Salaam', 'marital_status' => 'single', 'date_of_birth' => '1995-01-15', 'membership_date' => '2019-10-05', 'member_code' => 'MEM-008', 'remarks' => 'Usher team', 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Martha Mlowe', 'phone' => '0712001009', 'tithe_code' => 'TAG-009', 'gender' => 'female', 'zone' => 'Mikocheni', 'residency' => 'Dar es Salaam', 'marital_status' => 'married', 'date_of_birth' => '1987-02-20', 'membership_date' => '2017-05-01', 'member_code' => 'MEM-009', 'remarks' => 'Children church', 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Paulo Mhando', 'phone' => '0712001010', 'tithe_code' => 'TAG-010', 'gender' => 'male', 'zone' => 'Mikocheni', 'residency' => 'Dar es Salaam', 'marital_status' => 'single', 'date_of_birth' => '1993-10-13', 'membership_date' => '2018-09-16', 'member_code' => 'MEM-010', 'remarks' => 'Intercessor', 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Aneth Lyimo', 'phone' => '0712001011', 'tithe_code' => 'TAG-011', 'gender' => 'female', 'zone' => 'Mbezi', 'residency' => 'Dar es Salaam', 'marital_status' => 'single', 'date_of_birth' => '2000-06-30', 'membership_date' => '2022-02-20', 'member_code' => 'MEM-011', 'remarks' => 'Protocol team', 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Daniel Mnyawi', 'phone' => '0712001012', 'tithe_code' => 'TAG-012', 'gender' => 'male', 'zone' => 'Kimara', 'residency' => 'Dar es Salaam', 'marital_status' => 'married', 'date_of_birth' => '1981-08-09', 'membership_date' => '2014-03-02', 'member_code' => 'MEM-012', 'remarks' => 'Finance committee', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        $memberIds = DB::table('members')->pluck('id')->values();

        if (DB::table('families')->count() === 0) {
            DB::table('families')->insert([
                ['head_of_family' => 'Emmanuel Mwakalinga', 'gender' => 'male', 'phone' => '0712001001', 'members' => 5, 'created_at' => $now, 'updated_at' => $now],
                ['head_of_family' => 'Asha Mwajuma', 'gender' => 'female', 'phone' => '0712001005', 'members' => 4, 'created_at' => $now, 'updated_at' => $now],
                ['head_of_family' => 'Josephat Mrema', 'gender' => 'male', 'phone' => '0712001006', 'members' => 6, 'created_at' => $now, 'updated_at' => $now],
                ['head_of_family' => 'Martha Mlowe', 'gender' => 'female', 'phone' => '0712001009', 'members' => 3, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('departments')->count() === 0) {
            DB::table('departments')->insert([
                ['name' => 'Worship', 'leader_id' => $userId, 'description' => 'Praise and worship ministry', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Finance', 'leader_id' => $userId, 'description' => 'Stewardship and finance operations', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Youth', 'leader_id' => $userId, 'description' => 'Youth discipleship and outreach', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Protocol', 'leader_id' => $userId, 'description' => 'Order and guest reception', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('zones')->count() === 0) {
            DB::table('zones')->insert([
                ['name' => 'Mbezi', 'leader_id' => $userId, 'description' => 'Mbezi local fellowship', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Kimara', 'leader_id' => $userId, 'description' => 'Kimara local fellowship', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Sinza', 'leader_id' => $userId, 'description' => 'Sinza local fellowship', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Mikocheni', 'leader_id' => $userId, 'description' => 'Mikocheni local fellowship', 'status' => 'active', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        $departmentIds = DB::table('departments')->pluck('id')->values();
        $zoneIds = DB::table('zones')->pluck('id')->values();

        if (DB::table('department_members')->count() === 0 && $departmentIds->isNotEmpty() && $memberIds->isNotEmpty()) {
            $departmentMembers = [];
            foreach ($memberIds->take(10) as $index => $memberId) {
                $departmentMembers[] = [
                    'department_id' => $departmentIds[$index % $departmentIds->count()],
                    'member_id' => $memberId,
                    'role' => $index < 2 ? 'leader' : 'member',
                    'status' => 'active',
                    'joined_at' => now()->subDays(60 - $index),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('department_members')->insert($departmentMembers);
        }

        if (DB::table('zone_members')->count() === 0 && $zoneIds->isNotEmpty() && $memberIds->isNotEmpty()) {
            $zoneMembers = [];
            foreach ($memberIds as $index => $memberId) {
                $zoneMembers[] = [
                    'zone_id' => $zoneIds[$index % $zoneIds->count()],
                    'member_id' => $memberId,
                    'status' => 'active',
                    'joined_at' => now()->subDays(90 - $index),
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            DB::table('zone_members')->insert($zoneMembers);
        }

        if (DB::table('services')->count() === 0) {
            DB::table('services')->insert([
                ['name' => 'Sunday First Service', 'service_type' => 'Sunday', 'service_date' => now()->subDays(7)->toDateString(), 'start_time' => '07:00', 'end_time' => '09:00', 'location' => 'TAG Main Sanctuary', 'description' => 'Morning service', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Sunday Second Service', 'service_type' => 'Sunday', 'service_date' => now()->toDateString(), 'start_time' => '10:00', 'end_time' => '13:00', 'location' => 'TAG Main Sanctuary', 'description' => 'Main celebration service', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Friday Family Prayer', 'service_type' => 'Prayer', 'service_date' => now()->addDays(2)->toDateString(), 'start_time' => '17:30', 'end_time' => '21:00', 'location' => 'TAG Prayer Hall', 'description' => 'Family intercession', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        $serviceIds = DB::table('services')->pluck('id')->values();

        if (DB::table('visitors')->count() === 0) {
            DB::table('visitors')->insert([
                ['full_name' => 'Happiness Lema', 'phone' => '0755001001', 'email' => 'happiness.lema@example.com', 'gender' => 'female', 'address' => 'Mwenge, Dar es Salaam', 'invited_by' => 'Neema Kaseke', 'first_visit_date' => now()->subDays(10)->toDateString(), 'service_id' => $serviceIds[0] ?? null, 'status' => 'new', 'notes' => 'Interested in women fellowship', 'converted_member_id' => null, 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Amani Juma', 'phone' => '0755001002', 'email' => 'amani.juma@example.com', 'gender' => 'male', 'address' => 'Kimara, Dar es Salaam', 'invited_by' => 'Baraka Chacha', 'first_visit_date' => now()->subDays(5)->toDateString(), 'service_id' => $serviceIds[1] ?? null, 'status' => 'follow_up', 'notes' => 'Requested counseling', 'converted_member_id' => null, 'created_at' => $now, 'updated_at' => $now],
                ['full_name' => 'Stella Ndege', 'phone' => '0755001003', 'email' => 'stella.ndege@example.com', 'gender' => 'female', 'address' => 'Mbezi, Dar es Salaam', 'invited_by' => 'Martha Mlowe', 'first_visit_date' => now()->subDays(18)->toDateString(), 'service_id' => $serviceIds[0] ?? null, 'status' => 'converted', 'notes' => 'Completed membership class', 'converted_member_id' => $memberIds[2] ?? null, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        $visitorIds = DB::table('visitors')->pluck('id')->values();

        if (DB::table('follow_up_tasks')->count() === 0) {
            DB::table('follow_up_tasks')->insert([
                ['person_type' => 'visitor', 'person_id' => $visitorIds[0] ?? 1, 'assigned_to' => $userId, 'task_type' => 'first_call', 'priority' => 'high', 'due_date' => now()->addDays(1)->toDateString(), 'status' => 'pending', 'notes' => 'Welcome and pray with family', 'created_at' => $now, 'updated_at' => $now],
                ['person_type' => 'member', 'person_id' => $memberIds[3] ?? 1, 'assigned_to' => $userId, 'task_type' => 'pastoral_visit', 'priority' => 'medium', 'due_date' => now()->addDays(3)->toDateString(), 'status' => 'pending', 'notes' => 'Encouragement visit', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('follow_up_history')->count() === 0) {
            $taskIds = DB::table('follow_up_tasks')->pluck('id')->values();
            DB::table('follow_up_history')->insert([
                ['task_id' => $taskIds[0] ?? 1, 'action_taken' => 'Initial call completed', 'notes' => 'Visitor appreciated follow-up', 'created_by' => $userId, 'created_at' => now()->subDay()],
                ['task_id' => $taskIds[1] ?? 1, 'action_taken' => 'Visit scheduled', 'notes' => 'Set for Saturday afternoon', 'created_by' => $userId, 'created_at' => now()],
            ]);
        }

        if (DB::table('attendance_records')->count() === 0) {
            DB::table('attendance_records')->insert([
                ['service_id' => $serviceIds[0] ?? 1, 'member_id' => $memberIds[0] ?? null, 'visitor_id' => null, 'family_id' => null, 'department_id' => null, 'attendance_status' => 'present', 'recorded_by' => $userId, 'zone' => 'Mbezi', 'recorded_at' => now()->subDays(7), 'created_at' => $now, 'updated_at' => $now],
                ['service_id' => $serviceIds[0] ?? 1, 'member_id' => $memberIds[1] ?? null, 'visitor_id' => null, 'family_id' => null, 'department_id' => null, 'attendance_status' => 'present', 'recorded_by' => $userId, 'zone' => 'Mbezi', 'recorded_at' => now()->subDays(7), 'created_at' => $now, 'updated_at' => $now],
                ['service_id' => $serviceIds[1] ?? 1, 'member_id' => null, 'visitor_id' => $visitorIds[0] ?? null, 'family_id' => null, 'department_id' => null, 'attendance_status' => 'present', 'recorded_by' => $userId, 'zone' => 'Kimara', 'recorded_at' => now(), 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('pastoral_cases')->count() === 0) {
            DB::table('pastoral_cases')->insert([
                ['member_id' => $memberIds[0] ?? null, 'case_type' => 'counseling', 'priority' => 'high', 'status' => 'open', 'assigned_to' => $userId, 'opened_at' => now()->subDays(4), 'summary' => 'Marriage counseling support', 'created_at' => $now, 'updated_at' => $now],
                ['member_id' => $memberIds[4] ?? null, 'case_type' => 'hospital_visit', 'priority' => 'medium', 'status' => 'open', 'assigned_to' => $userId, 'opened_at' => now()->subDays(2), 'summary' => 'Prayer and support follow-up', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('pastoral_case_notes')->count() === 0) {
            $caseIds = DB::table('pastoral_cases')->pluck('id')->values();
            DB::table('pastoral_case_notes')->insert([
                ['case_id' => $caseIds[0] ?? 1, 'note' => 'Family meeting held and prayer offered.', 'visibility' => 'private', 'created_by' => $userId, 'created_at' => now()->subDays(3)],
                ['case_id' => $caseIds[1] ?? 1, 'note' => 'Hospital visit planned for this week.', 'visibility' => 'private', 'created_by' => $userId, 'created_at' => now()->subDay()],
            ]);
        }

        if (DB::table('prayer_requests')->count() === 0) {
            DB::table('prayer_requests')->insert([
                ['member_id' => $memberIds[1] ?? null, 'visitor_id' => null, 'request_type' => 'thanksgiving', 'request_text' => 'Thank God for new job opportunity.', 'visibility' => 'public', 'status' => 'open', 'assigned_to' => $userId, 'created_at' => $now, 'updated_at' => $now],
                ['member_id' => null, 'visitor_id' => $visitorIds[1] ?? null, 'request_type' => 'healing', 'request_text' => 'Prayer for mother recovering from surgery.', 'visibility' => 'private', 'status' => 'open', 'assigned_to' => $userId, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('alerts')->count() === 0) {
            DB::table('alerts')->insert([
                ['alert_type' => 'follow_up_overdue', 'reference_type' => 'follow_up_task', 'reference_id' => '1', 'title' => 'Follow-up pending for visitor', 'message' => 'Initial call not completed within 48 hours.', 'severity' => 'high', 'assigned_to' => $userId, 'status' => 'open', 'due_at' => now()->addDay(), 'created_at' => $now, 'updated_at' => $now],
                ['alert_type' => 'pledge_due', 'reference_type' => 'pledge', 'reference_id' => '1', 'title' => 'Pledge installment overdue', 'message' => 'Member has an unpaid pledge installment.', 'severity' => 'medium', 'assigned_to' => $userId, 'status' => 'open', 'due_at' => now()->addDays(3), 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('member_timeline_events')->count() === 0 && $memberIds->isNotEmpty()) {
            DB::table('member_timeline_events')->insert([
                ['member_id' => $memberIds[0], 'event_type' => 'membership', 'event_date' => now()->subMonths(12), 'title' => 'Joined TAG church', 'details' => 'Completed new members class.', 'created_at' => $now, 'updated_at' => $now],
                ['member_id' => $memberIds[1], 'event_type' => 'ministry', 'event_date' => now()->subMonths(2), 'title' => 'Joined worship team', 'details' => 'Assigned to Sunday second service.', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('communications')->count() === 0) {
            DB::table('communications')->insert([
                ['channel' => 'sms', 'audience_type' => 'members', 'subject' => 'Friday Prayer Reminder', 'message' => 'Karibu ibada ya maombi ya familia Ijumaa saa 11:30 jioni.', 'status' => 'sent', 'created_by' => $userId, 'sent_at' => now()->subHours(6), 'created_at' => $now, 'updated_at' => $now],
                ['channel' => 'email', 'audience_type' => 'leaders', 'subject' => 'Monthly leadership review', 'message' => 'Please prepare reports for Sunday leadership meeting.', 'status' => 'draft', 'created_by' => $userId, 'sent_at' => null, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('communication_deliveries')->count() === 0) {
            $communicationIds = DB::table('communications')->pluck('id')->values();
            DB::table('communication_deliveries')->insert([
                ['communication_id' => $communicationIds[0] ?? 1, 'recipient_type' => 'member', 'recipient_id' => $memberIds[0] ?? 1, 'recipient_contact' => '0712001001', 'delivery_status' => 'delivered', 'delivered_at' => now()->subHours(5), 'created_at' => $now, 'updated_at' => $now],
                ['communication_id' => $communicationIds[0] ?? 1, 'recipient_type' => 'member', 'recipient_id' => $memberIds[1] ?? 1, 'recipient_contact' => '0712001002', 'delivery_status' => 'delivered', 'delivered_at' => now()->subHours(5), 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('events')->count() === 0) {
            DB::table('events')->insert([
                ['title' => 'TAG Youth Revival', 'event_type' => 'revival', 'description' => 'Three-day youth revival and discipleship training.', 'start_date' => now()->addDays(10)->toDateString(), 'end_date' => now()->addDays(12)->toDateString(), 'start_time' => '17:00', 'end_time' => '21:00', 'location' => 'TAG Main Auditorium', 'status' => 'planned', 'created_by' => $userId, 'created_at' => $now, 'updated_at' => $now],
                ['title' => 'Women Ministry Conference', 'event_type' => 'conference', 'description' => 'Empowerment and prayer conference.', 'start_date' => now()->addDays(20)->toDateString(), 'end_date' => now()->addDays(21)->toDateString(), 'start_time' => '09:00', 'end_time' => '16:00', 'location' => 'TAG Hall B', 'status' => 'planned', 'created_by' => $userId, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        $eventIds = DB::table('events')->pluck('id')->values();

        if (DB::table('event_registrations')->count() === 0 && $eventIds->isNotEmpty()) {
            DB::table('event_registrations')->insert([
                ['event_id' => $eventIds[0], 'member_id' => $memberIds[2] ?? null, 'visitor_id' => null, 'status' => 'registered', 'registered_at' => now()->subDays(1), 'created_at' => $now, 'updated_at' => $now],
                ['event_id' => $eventIds[0], 'member_id' => $memberIds[3] ?? null, 'visitor_id' => null, 'status' => 'registered', 'registered_at' => now()->subDays(1), 'created_at' => $now, 'updated_at' => $now],
                ['event_id' => $eventIds[1] ?? $eventIds[0], 'member_id' => null, 'visitor_id' => $visitorIds[0] ?? null, 'status' => 'registered', 'registered_at' => now()->subDays(1), 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('volunteer_assignments')->count() === 0 && $eventIds->isNotEmpty()) {
            DB::table('volunteer_assignments')->insert([
                ['member_id' => $memberIds[0] ?? 1, 'event_id' => $eventIds[0], 'department_id' => $departmentIds[0] ?? null, 'role' => 'Coordinator', 'report_time' => now()->addDays(10)->setTime(16, 0), 'status' => 'assigned', 'notes' => 'Lead worship team logistics.', 'created_at' => $now, 'updated_at' => $now],
                ['member_id' => $memberIds[1] ?? 1, 'event_id' => $eventIds[0], 'department_id' => $departmentIds[3] ?? null, 'role' => 'Reception', 'report_time' => now()->addDays(10)->setTime(16, 30), 'status' => 'assigned', 'notes' => 'Welcome desk setup.', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('employees')->count() === 0) {
            DB::table('employees')->insert([
                ['name' => 'Pastor Joel Mushi', 'designation' => 'Lead Pastor', 'phone' => '0733001001', 'account_name' => 'Joel Mushi', 'account_number' => '015400123456', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Mary Nchimbi', 'designation' => 'Finance Officer', 'phone' => '0733001002', 'account_name' => 'Mary Nchimbi', 'account_number' => '015400123457', 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Deus Mhando', 'designation' => 'Worship Director', 'phone' => '0733001003', 'account_name' => 'Deus Mhando', 'account_number' => '015400123458', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('payroll_categories')->count() === 0) {
            DB::table('payroll_categories')->insert([
                ['name' => 'Housing Allowance', 'type' => 'Addition', 'charge_in' => 'Amount', 'charge' => 150000, 'deduct_after_paye' => false, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Transport Allowance', 'type' => 'Addition', 'charge_in' => 'Amount', 'charge' => 80000, 'deduct_after_paye' => false, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Social Security', 'type' => 'Deduction', 'charge_in' => 'Percent', 'charge' => 10, 'deduct_after_paye' => true, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('payrolls')->count() === 0) {
            $employees = DB::table('employees')->get();
            $rows = [];
            foreach ($employees as $employee) {
                $salary = 1200000;
                $taxPercent = 9;
                $addition = 200000;
                $other = 50000;
                $gross = $salary + $addition + $other;
                $paye = ($gross * $taxPercent) / 100;
                $net = $gross - $paye;
                $rows[] = [
                    'employee_id' => $employee->id,
                    'payment_date' => now()->subDays(3)->toDateString(),
                    'method' => 'bank',
                    'account_name' => $employee->account_name,
                    'account_number' => $employee->account_number,
                    'salary' => $salary,
                    'tax_percent' => $taxPercent,
                    'church_staffs_addition' => $addition,
                    'paye' => $paye,
                    'other_amount' => $other,
                    'net_salary' => $net,
                    'take_home' => $net,
                    'paid_amount' => $net,
                    'details' => 'Monthly salary for TAG church staff',
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (! empty($rows)) {
                DB::table('payrolls')->insert($rows);
            }
        }

        if (DB::table('expenditures')->count() === 0) {
            DB::table('expenditures')->insert([
                ['date' => now()->subDays(14)->toDateString(), 'description' => 'Sound system maintenance', 'amount' => 350000, 'category' => 'Operations', 'notes' => 'Quarterly maintenance', 'created_at' => $now, 'updated_at' => $now],
                ['date' => now()->subDays(8)->toDateString(), 'description' => 'Youth outreach transport', 'amount' => 120000, 'category' => 'Outreach', 'notes' => 'Bus hire to Mbezi zone', 'created_at' => $now, 'updated_at' => $now],
                ['date' => now()->subDays(2)->toDateString(), 'description' => 'Electricity bill', 'amount' => 480000, 'category' => 'Utilities', 'notes' => 'Main sanctuary and offices', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('department_incomes')->count() === 0) {
            DB::table('department_incomes')->insert([
                ['department' => 'Worship', 'income_type' => 'Special Offering', 'amount' => 450000, 'received_date' => now()->subDays(10)->toDateString(), 'comment' => 'Choir thanksgiving seed', 'created_at' => $now, 'updated_at' => $now],
                ['department' => 'Youth', 'income_type' => 'Fundraiser', 'amount' => 620000, 'received_date' => now()->subDays(6)->toDateString(), 'comment' => 'Revival preparation fundraiser', 'created_at' => $now, 'updated_at' => $now],
                ['department' => 'Finance', 'income_type' => 'Partner Support', 'amount' => 800000, 'received_date' => now()->subDays(3)->toDateString(), 'comment' => 'Mission partner transfer', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('income_types')->count() === 0) {
            DB::table('income_types')->insert([
                ['type' => 'Tithe', 'created_at' => $now, 'updated_at' => $now],
                ['type' => 'Offering', 'created_at' => $now, 'updated_at' => $now],
                ['type' => 'Thanksgiving', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('incomes')->count() === 0) {
            $incomeTypeIds = DB::table('income_types')->pluck('id')->values();
            DB::table('incomes')->insert([
                ['income_type_id' => $incomeTypeIds[0] ?? 1, 'amount' => 900000, 'received_date' => now()->subDays(5)->toDateString(), 'member_id' => $memberIds[0] ?? null, 'comment' => 'Monthly tithe', 'created_at' => $now, 'updated_at' => $now],
                ['income_type_id' => $incomeTypeIds[1] ?? 1, 'amount' => 650000, 'received_date' => now()->subDays(3)->toDateString(), 'member_id' => $memberIds[1] ?? null, 'comment' => 'Sunday offering', 'created_at' => $now, 'updated_at' => $now],
                ['income_type_id' => $incomeTypeIds[2] ?? 1, 'amount' => 300000, 'received_date' => now()->subDay()->toDateString(), 'member_id' => $memberIds[2] ?? null, 'comment' => 'Thanksgiving seed', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('campaigns')->count() === 0) {
            DB::table('campaigns')->insert([
                ['name' => 'Sanctuary Renovation', 'description' => 'Upgrade church auditorium and media desk.', 'start_date' => now()->subMonths(1)->toDateString(), 'end_date' => now()->addMonths(2)->toDateString(), 'target_amount' => 25000000, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Youth Revival 2026', 'description' => 'Support youth discipleship revival.', 'start_date' => now()->subDays(20)->toDateString(), 'end_date' => now()->addDays(40)->toDateString(), 'target_amount' => 8000000, 'created_at' => $now, 'updated_at' => $now],
                ['name' => 'Community Feeding Program', 'description' => 'Monthly outreach support in Dar es Salaam.', 'start_date' => now()->subDays(10)->toDateString(), 'end_date' => now()->addMonths(3)->toDateString(), 'target_amount' => 12000000, 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        $campaignIds = DB::table('campaigns')->pluck('id')->values();

        if (DB::table('donations')->count() === 0) {
            DB::table('donations')->insert([
                ['donor_name' => 'Emmanuel Mwakalinga', 'donor_email' => 'emmanuel@example.com', 'amount' => 500000, 'method' => 'bank', 'donation_date' => now()->subDays(9)->toDateString(), 'campaign_id' => $campaignIds[0] ?? null, 'notes' => 'Family pledge support', 'created_at' => $now, 'updated_at' => $now],
                ['donor_name' => 'Neema Kaseke', 'donor_email' => 'neema@example.com', 'amount' => 250000, 'method' => 'mobile_money', 'donation_date' => now()->subDays(7)->toDateString(), 'campaign_id' => $campaignIds[1] ?? null, 'notes' => 'Youth revival support', 'created_at' => $now, 'updated_at' => $now],
                ['donor_name' => 'Josephat Mrema', 'donor_email' => 'josephat@example.com', 'amount' => 800000, 'method' => 'cash', 'donation_date' => now()->subDays(4)->toDateString(), 'campaign_id' => $campaignIds[0] ?? null, 'notes' => 'Sanctuary phase 1', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('pledges')->count() === 0) {
            DB::table('pledges')->insert([
                ['pledger_name' => 'Martha Mlowe', 'pledger_email' => 'martha@example.com', 'amount' => 1200000, 'pledge_date' => now()->subDays(12)->toDateString(), 'campaign_id' => $campaignIds[0] ?? null, 'notes' => 'Paid in installments', 'created_at' => $now, 'updated_at' => $now],
                ['pledger_name' => 'Paulo Mhando', 'pledger_email' => 'paulo@example.com', 'amount' => 500000, 'pledge_date' => now()->subDays(10)->toDateString(), 'campaign_id' => $campaignIds[1] ?? null, 'notes' => 'Youth revival support', 'created_at' => $now, 'updated_at' => $now],
                ['pledger_name' => 'Aneth Lyimo', 'pledger_email' => 'aneth@example.com', 'amount' => 350000, 'pledge_date' => now()->subDays(6)->toDateString(), 'campaign_id' => $campaignIds[2] ?? null, 'notes' => 'Community feeding support', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        $pledgeIds = DB::table('pledges')->pluck('id')->values();

        if (DB::table('pledge_payments')->count() === 0 && $pledgeIds->isNotEmpty()) {
            DB::table('pledge_payments')->insert([
                ['pledge_id' => $pledgeIds[0], 'campaign_id' => $campaignIds[0] ?? null, 'phone' => '0712001009', 'invoice_number' => 'TAG-INV-1001', 'amount' => 500000, 'payment_date' => now()->subDays(8)->toDateString(), 'method' => 'bank', 'notes' => 'First installment', 'created_at' => $now, 'updated_at' => $now],
                ['pledge_id' => $pledgeIds[0], 'campaign_id' => $campaignIds[0] ?? null, 'phone' => '0712001009', 'invoice_number' => 'TAG-INV-1002', 'amount' => 300000, 'payment_date' => now()->subDays(3)->toDateString(), 'method' => 'mobile_money', 'notes' => 'Second installment', 'created_at' => $now, 'updated_at' => $now],
                ['pledge_id' => $pledgeIds[1] ?? $pledgeIds[0], 'campaign_id' => $campaignIds[1] ?? null, 'phone' => '0712001010', 'invoice_number' => 'TAG-INV-1003', 'amount' => 200000, 'payment_date' => now()->subDays(2)->toDateString(), 'method' => 'cash', 'notes' => 'Partial payment', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('missed_pledges')->count() === 0 && $pledgeIds->isNotEmpty()) {
            DB::table('missed_pledges')->insert([
                ['pledge_id' => $pledgeIds[1] ?? $pledgeIds[0], 'missed_date' => now()->subDays(1)->toDateString(), 'reason' => 'Salary delayed', 'created_at' => $now, 'updated_at' => $now],
            ]);
        }

        if (DB::table('audit_logs')->count() === 0) {
            DB::table('audit_logs')->insert([
                ['user_id' => $userId, 'actor_username' => DB::table('users')->where('id', $userId)->value('username'), 'action' => 'seed.sample_data', 'entity_type' => 'system', 'entity_id' => 'tag-sample', 'after_json' => json_encode(['scope' => 'all-modules', 'context' => 'TAG Tanzania']), 'ip_address' => '127.0.0.1', 'route_name' => 'artisan.db.seed', 'method' => 'CLI', 'created_at' => now()],
            ]);
        }
    }

    private function ensureSystemUser(): int
    {
        $existing = DB::table('users')->where('username', 'brr')->value('id');
        if ($existing) {
            return (int) $existing;
        }

        $active = DB::table('users')->where('status', 'active')->value('id');
        if ($active) {
            return (int) $active;
        }

        return (int) DB::table('users')->insertGetId([
            'name' => 'TAG System Admin',
            'username' => 'tag.admin',
            'full_name' => 'TAG System Administrator',
            'email' => 'tag.admin@bcc.local',
            'password' => Hash::make('Password123!'),
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
