<?php

namespace Tests\Feature;

use App\Models\Communication;
use App\Models\Department;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use App\Models\Visitor;
use App\Models\VolunteerAssignment;
use App\Models\Zone;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseFourLeadershipTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_admin_can_run_phase_four_core_flows(): void
    {
        $admin = $this->actingAsRole('system_admin');
        $member = Member::query()->create(['full_name' => 'Volunteer Member', 'gender' => 'Male']);
        $visitor = Visitor::query()->create(['full_name' => 'Visitor Person', 'status' => 'new']);

        $this->actingAs($admin)->post(route('communications.store'), [
            'channel' => 'sms',
            'audience_type' => 'everyone',
            'subject' => 'Phase 4',
            'message' => 'Leadership update',
        ])->assertRedirect();

        $communication = \App\Models\Communication::query()->firstOrFail();
        $this->actingAs($admin)->post(route('communications.send', $communication))->assertRedirect();

        $this->actingAs($admin)->post(route('events.store'), [
            'title' => 'Leadership Gathering',
            'event_type' => 'Leadership',
            'start_date' => now()->toDateString(),
            'status' => 'planned',
        ])->assertRedirect();

        $event = \App\Models\Event::query()->firstOrFail();

        $this->actingAs($admin)->post(route('events.registrations.store', $event), [
            'member_id' => $member->id,
            'status' => 'registered',
        ])->assertRedirect();

        $this->actingAs($admin)->post(route('volunteers.assignments.store'), [
            'member_id' => $member->id,
            'event_id' => $event->id,
            'role' => 'Protocol',
            'status' => 'assigned',
        ])->assertRedirect();

        $this->actingAs($admin)->get(route('reports.zones'))->assertOk();
        $this->actingAs($admin)->get(route('reports.departments'))->assertOk();

        $this->assertDatabaseHas('communications', ['subject' => 'Phase 4', 'status' => 'sent']);
        $this->assertDatabaseHas('communication_deliveries', ['recipient_type' => 'member', 'recipient_id' => $member->id]);
        $this->assertDatabaseHas('communication_deliveries', ['recipient_type' => 'visitor', 'recipient_id' => $visitor->id]);
        $this->assertDatabaseHas('events', ['title' => 'Leadership Gathering']);
        $this->assertDatabaseHas('event_registrations', ['event_id' => $event->id, 'member_id' => $member->id]);
        $this->assertDatabaseHas('volunteer_assignments', ['member_id' => $member->id, 'event_id' => $event->id]);
    }

    public function test_system_admin_can_update_and_delete_phase_four_records(): void
    {
        $admin = $this->actingAsRole('system_admin');
        $member = Member::query()->create(['full_name' => 'Editable Member', 'gender' => 'Female']);

        $this->actingAs($admin)->post(route('communications.store'), [
            'channel' => 'sms',
            'audience_type' => 'all_members',
            'subject' => 'Draft Subject',
            'message' => 'Initial draft message',
        ])->assertRedirect();

        $communication = Communication::query()->firstOrFail();

        $this->actingAs($admin)->put(route('communications.update', $communication), [
            'channel' => 'email',
            'audience_type' => 'all_members',
            'subject' => 'Updated Subject',
            'message' => 'Updated message',
        ])->assertRedirect();

        $this->assertDatabaseHas('communications', [
            'id' => $communication->id,
            'channel' => 'email',
            'subject' => 'Updated Subject',
        ]);

        $this->actingAs($admin)->post(route('events.store'), [
            'title' => 'Editable Event',
            'event_type' => 'Leadership',
            'start_date' => now()->toDateString(),
            'status' => 'planned',
        ])->assertRedirect();

        $event = Event::query()->firstOrFail();

        $this->actingAs($admin)->put(route('events.update', $event), [
            'title' => 'Editable Event Updated',
            'event_type' => 'Leadership',
            'description' => 'Updated description',
            'start_date' => now()->toDateString(),
            'status' => 'ongoing',
        ])->assertRedirect();

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'title' => 'Editable Event Updated',
            'status' => 'ongoing',
        ]);

        $this->actingAs($admin)->post(route('volunteers.assignments.store'), [
            'member_id' => $member->id,
            'event_id' => $event->id,
            'role' => 'Usher',
            'status' => 'assigned',
        ])->assertRedirect();

        $assignment = VolunteerAssignment::query()->firstOrFail();

        $this->actingAs($admin)->put(route('volunteers.assignments.update', $assignment), [
            'member_id' => $member->id,
            'event_id' => $event->id,
            'role' => 'Usher',
            'status' => 'confirmed',
        ])->assertRedirect();

        $this->assertDatabaseHas('volunteer_assignments', [
            'id' => $assignment->id,
            'status' => 'confirmed',
        ]);

        $this->actingAs($admin)->delete(route('volunteers.assignments.destroy', $assignment))->assertRedirect();
        $this->assertDatabaseMissing('volunteer_assignments', ['id' => $assignment->id]);

        $this->actingAs($admin)->delete(route('communications.destroy', $communication))->assertRedirect(route('communications.index'));
        $this->assertDatabaseMissing('communications', ['id' => $communication->id]);

        $this->actingAs($admin)->delete(route('events.destroy', $event))->assertRedirect(route('events.index'));
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    public function test_system_admin_can_filter_events_and_volunteers_by_department_and_zone(): void
    {
        $admin = $this->actingAsRole('system_admin');

        Zone::query()->create(['name' => 'Zone A', 'status' => 'active']);
        Zone::query()->create(['name' => 'Zone B', 'status' => 'active']);
        $departmentA = Department::query()->create(['name' => 'Protocol', 'status' => 'active']);
        $departmentB = Department::query()->create(['name' => 'Worship', 'status' => 'active']);

        $memberA = Member::query()->create(['full_name' => 'Member Zone A', 'gender' => 'Male', 'zone' => 'Zone A']);
        $memberB = Member::query()->create(['full_name' => 'Member Zone B', 'gender' => 'Female', 'zone' => 'Zone B']);

        $eventA = Event::query()->create([
            'title' => 'Event A',
            'event_type' => 'Leadership',
            'start_date' => now()->toDateString(),
            'status' => 'planned',
        ]);
        $eventB = Event::query()->create([
            'title' => 'Event B',
            'event_type' => 'Leadership',
            'start_date' => now()->toDateString(),
            'status' => 'planned',
        ]);

        EventRegistration::query()->create([
            'event_id' => $eventA->id,
            'member_id' => $memberA->id,
            'status' => 'registered',
            'registered_at' => now(),
        ]);
        EventRegistration::query()->create([
            'event_id' => $eventB->id,
            'member_id' => $memberB->id,
            'status' => 'registered',
            'registered_at' => now(),
        ]);

        VolunteerAssignment::query()->create([
            'member_id' => $memberA->id,
            'event_id' => $eventA->id,
            'department_id' => $departmentA->id,
            'role' => 'Role Zone A',
            'status' => 'assigned',
        ]);
        VolunteerAssignment::query()->create([
            'member_id' => $memberB->id,
            'event_id' => $eventB->id,
            'department_id' => $departmentB->id,
            'role' => 'Role Zone B',
            'status' => 'assigned',
        ]);

        $this->actingAs($admin)
            ->get(route('events.index', ['department_id' => $departmentA->id, 'zone' => 'Zone A']))
            ->assertOk()
            ->assertSee('Event A')
            ->assertDontSee('Event B');

        $this->actingAs($admin)
            ->get(route('volunteers.index', ['department_id' => $departmentA->id, 'zone' => 'Zone A']))
            ->assertOk()
            ->assertSee('Role Zone A')
            ->assertDontSee('Role Zone B');
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
