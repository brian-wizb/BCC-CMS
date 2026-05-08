<?php

namespace Tests\Feature;

use App\Models\Alert;
use App\Models\AttendanceRecord;
use App\Models\Member;
use App\Models\Role;
use App\Models\Service;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseThreeMemberLifecycleTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_admin_can_run_alerts_and_manage_alert_state(): void
    {
        $user = $this->actingAsRole('member_admin');
        $member = Member::query()->create([
            'full_name' => 'No Attendance Member',
            'gender' => 'Female',
        ]);

        $this->actingAs($user)->post(route('alerts.run'))->assertRedirect(route('alerts.index'));

        $alert = Alert::query()
            ->where('reference_type', 'member')
            ->where('reference_id', (string) $member->id)
            ->firstOrFail();

        $this->actingAs($user)->put(route('alerts.update', $alert), [
            'status' => 'acknowledged',
            'severity' => 'high',
            'assigned_to' => $user->id,
            'due_at' => now()->addDay()->toDateTimeString(),
        ])->assertRedirect();

        $this->assertDatabaseHas('alerts', [
            'id' => $alert->id,
            'status' => 'acknowledged',
            'severity' => 'high',
            'assigned_to' => $user->id,
        ]);

        $this->actingAs($user)->delete(route('alerts.destroy', $alert))->assertRedirect(route('alerts.index'));

        $this->assertDatabaseMissing('alerts', ['id' => $alert->id]);
    }

    public function test_member_admin_can_view_member_timeline(): void
    {
        $user = $this->actingAsRole('member_admin');
        $member = Member::query()->create([
            'full_name' => 'Timeline Member',
            'gender' => 'Male',
            'membership_date' => now()->toDateString(),
        ]);

        $service = Service::query()->create([
            'name' => 'Timeline Service',
            'service_type' => 'Sunday Service',
            'service_date' => now()->toDateString(),
        ]);

        AttendanceRecord::query()->create([
            'service_id' => $service->id,
            'member_id' => $member->id,
            'attendance_status' => 'present',
            'recorded_by' => $user->id,
            'recorded_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('members.timeline', $member))
            ->assertOk()
            ->assertSee('Timeline Member timeline')
            ->assertSee('Membership Date')
            ->assertSee('Attendance: Present');
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
