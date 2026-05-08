<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseTwoMinistryCareTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_admin_can_create_visitor_follow_up_service_attendance_case_and_prayer_request(): void
    {
        $user = $this->actingAsRole('member_admin');

        $visitorCreate = $this->actingAs($user)->post(route('visitors.store'), [
            'full_name' => 'Phase Two Visitor',
            'phone' => '0700000001',
            'status' => 'new',
        ]);
        $visitorCreate->assertRedirect();
        $visitorId = (int) \App\Models\Visitor::query()->where('full_name', 'Phase Two Visitor')->value('id');

        $this->actingAs($user)->post(route('follow-up.tasks.store'), [
            'person_type' => 'visitor',
            'person_id' => $visitorId,
            'task_type' => 'call',
            'priority' => 'high',
            'status' => 'pending',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('attendance.services.store'), [
            'name' => 'Phase Two Sunday',
            'service_type' => 'Sunday Service',
            'service_date' => now()->toDateString(),
        ])->assertRedirect();

        $serviceId = (int) \App\Models\Service::query()->where('name', 'Phase Two Sunday')->value('id');

        $this->actingAs($user)->post(route('attendance.record.store'), [
            'service_id' => $serviceId,
            'visitor_id' => $visitorId,
            'attendance_status' => 'present',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('pastoral-care.store'), [
            'case_type' => 'Counseling',
            'priority' => 'medium',
            'status' => 'open',
            'summary' => 'Needs counseling support',
        ])->assertRedirect();

        $this->actingAs($user)->post(route('prayer-requests.store'), [
            'visitor_id' => $visitorId,
            'request_type' => 'Healing',
            'request_text' => 'Pray for healing',
            'visibility' => 'private',
            'status' => 'open',
        ])->assertRedirect();

        $this->assertDatabaseHas('visitors', ['full_name' => 'Phase Two Visitor']);
        $this->assertDatabaseHas('follow_up_tasks', ['person_type' => 'visitor', 'person_id' => $visitorId]);
        $this->assertDatabaseHas('services', ['name' => 'Phase Two Sunday']);
        $this->assertDatabaseHas('attendance_records', ['service_id' => $serviceId, 'visitor_id' => $visitorId]);
        $this->assertDatabaseHas('pastoral_cases', ['case_type' => 'Counseling']);
        $this->assertDatabaseHas('prayer_requests', ['request_type' => 'Healing']);
    }

    public function test_accountant_cannot_create_visitors_or_pastoral_cases(): void
    {
        $user = $this->actingAsRole('accountant');

        $this->actingAs($user)->post(route('visitors.store'), [
            'full_name' => 'Blocked Visitor',
            'status' => 'new',
        ])->assertForbidden();

        $this->actingAs($user)->post(route('pastoral-care.store'), [
            'case_type' => 'Blocked Case',
            'priority' => 'low',
            'status' => 'open',
        ])->assertForbidden();
    }

    private function actingAsRole(string $roleKey): User
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create();
        $roleId = Role::query()->where('key', $roleKey)->value('id');
        $user->roles()->sync([$roleId]);

        return $user;
    }
}
