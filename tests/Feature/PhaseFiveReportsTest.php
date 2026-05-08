<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use App\Models\VolunteerAssignment;
use App\Models\Zone;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseFiveReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_admin_can_open_leadership_reports_pages(): void
    {
        $user = $this->actingAsRole('member_admin');

        Department::query()->create(['name' => 'Protocol', 'status' => 'active']);
        Zone::query()->create(['name' => 'Zone A', 'status' => 'active']);
        $member = Member::query()->create(['full_name' => 'Report Member', 'gender' => 'Male']);
        $event = Event::query()->create([
            'title' => 'Report Event',
            'event_type' => 'Leadership',
            'start_date' => now()->toDateString(),
            'status' => 'planned',
        ]);

        VolunteerAssignment::query()->create([
            'member_id' => $member->id,
            'event_id' => $event->id,
            'role' => 'Protocol',
            'status' => 'assigned',
        ]);

        $this->actingAs($user)->get(route('reports.index'))->assertOk()->assertSee('Department Report');
        $this->actingAs($user)->get(route('reports.departments'))->assertOk()->assertSee('Department Report');
        $this->actingAs($user)->get(route('reports.zones'))->assertOk()->assertSee('Zone Report');
        $this->actingAs($user)->get(route('reports.events'))->assertOk()->assertSee('Event Report');
        $this->actingAs($user)->get(route('reports.volunteers'))->assertOk()->assertSee('Volunteer Assignment Report');
        $this->actingAs($user)->get(route('reports.departments.export'))->assertOk()->assertHeader('content-disposition', 'attachment; filename=department-report.csv');
        $this->actingAs($user)->get(route('reports.zones.export'))->assertOk()->assertHeader('content-disposition', 'attachment; filename=zone-report.csv');
        $this->actingAs($user)->get(route('reports.events.export'))->assertOk()->assertHeader('content-disposition', 'attachment; filename=event-report.csv');
        $this->actingAs($user)->get(route('reports.volunteers.export'))->assertOk()->assertHeader('content-disposition', 'attachment; filename=volunteer-report.csv');
    }

    public function test_user_without_permission_cannot_open_leadership_reports_pages(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($user)->get(route('reports.departments.export'))->assertForbidden();
    }

    public function test_member_admin_can_filter_reports_by_department_and_zone(): void
    {
        $user = $this->actingAsRole('member_admin');

        Zone::query()->create(['name' => 'Zone A', 'status' => 'active']);
        Zone::query()->create(['name' => 'Zone B', 'status' => 'active']);
        $departmentA = Department::query()->create(['name' => 'Protocol', 'status' => 'active']);
        $departmentB = Department::query()->create(['name' => 'Worship', 'status' => 'active']);

        $memberA = Member::query()->create(['full_name' => 'Report Zone A', 'gender' => 'Male', 'zone' => 'Zone A']);
        $memberB = Member::query()->create(['full_name' => 'Report Zone B', 'gender' => 'Female', 'zone' => 'Zone B']);

        $eventA = Event::query()->create([
            'title' => 'Report Event A',
            'event_type' => 'Leadership',
            'start_date' => now()->toDateString(),
            'status' => 'planned',
        ]);
        $eventB = Event::query()->create([
            'title' => 'Report Event B',
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
            'role' => 'Protocol',
            'status' => 'assigned',
        ]);
        VolunteerAssignment::query()->create([
            'member_id' => $memberB->id,
            'event_id' => $eventB->id,
            'department_id' => $departmentB->id,
            'role' => 'Singer',
            'status' => 'assigned',
        ]);

        $query = ['department_id' => $departmentA->id, 'zone' => 'Zone A'];

        $this->actingAs($user)
            ->get(route('reports.events', $query))
            ->assertOk()
            ->assertSee('Report Event A')
            ->assertDontSee('Report Event B');

        $this->actingAs($user)
            ->get(route('reports.volunteers', $query))
            ->assertOk()
            ->assertSee('Report Zone A')
            ->assertDontSee('Report Zone B');

        $this->actingAs($user)
            ->get(route('reports.events.export', $query))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=event-report.csv');
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
