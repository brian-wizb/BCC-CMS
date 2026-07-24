<?php

namespace Tests\Feature;

use App\Models\Department;
use App\Models\Member;
use App\Models\Role;
use App\Models\User;
use App\Models\Zone;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseFiveReportsTest extends TestCase
{
    use RefreshDatabase;

    public function test_member_admin_can_open_leadership_reports_pages(): void
    {
        $user = $this->actingAsRole('chief_usher');

        Department::query()->create(['name' => 'Protocol', 'status' => 'active']);
        Zone::query()->create(['name' => 'Zone A', 'status' => 'active']);
        Member::query()->create(['full_name' => 'Report Member', 'gender' => 'Male']);

        $this->actingAs($user)->get(route('reports.index'))->assertOk()->assertSee('Department Report');
        $this->actingAs($user)->get(route('reports.departments'))->assertOk()->assertSee('Department Report');
        $this->actingAs($user)->get(route('reports.zones'))->assertOk()->assertSee('Zone Report');
        $this->actingAs($user)->get(route('reports.members'))->assertOk()->assertSee('Members Report');
        $this->actingAs($user)->get(route('reports.finance'))->assertOk()->assertSee('Finance Report');
        $this->actingAs($user)->get(route('reports.visitors'))->assertOk();
        $this->actingAs($user)->get(route('reports.pledges'))->assertOk();
        $this->actingAs($user)->get(route('reports.followup'))->assertOk();
        $this->actingAs($user)->get(route('reports.communications'))->assertOk();
        $this->actingAs($user)->get(route('reports.departments.export'))->assertOk()->assertHeader('content-disposition', 'attachment; filename=department-report.csv');
        $this->actingAs($user)->get(route('reports.zones.export'))->assertOk()->assertHeader('content-disposition', 'attachment; filename=zone-report.csv');
        $this->actingAs($user)->get(route('reports.members.export'))->assertOk()->assertHeader('content-disposition', 'attachment; filename=members-report.csv');
        $this->actingAs($user)->get(route('reports.finance.export'))->assertOk()->assertHeader('content-disposition', 'attachment; filename=finance-report.csv');
    }

    public function test_user_without_permission_cannot_open_leadership_reports_pages(): void
    {
        $this->seed(AuthorizationSeeder::class);
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('reports.index'))->assertForbidden();
        $this->actingAs($user)->get(route('reports.departments.export'))->assertForbidden();
    }

    public function test_member_admin_can_filter_reports_by_date_ranges(): void
    {
        $user = $this->actingAsRole('chief_usher');

        Zone::query()->create(['name' => 'Zone A', 'status' => 'active']);
        Zone::query()->create(['name' => 'Zone B', 'status' => 'active']);
        Member::query()->create(['full_name' => 'Report Zone A', 'gender' => 'Male', 'zone' => 'Zone A', 'membership_date' => now()->subDays(2)]);
        Member::query()->create(['full_name' => 'Report Zone B', 'gender' => 'Female', 'zone' => 'Zone B', 'membership_date' => now()->subYear()]);

        $query = [
            'date_from' => now()->subWeek()->toDateString(),
            'date_to' => now()->toDateString(),
        ];

        $this->actingAs($user)
            ->get(route('reports.members', $query))
            ->assertOk()
            ->assertSee('Report Zone A')
            ->assertDontSee('Report Zone B');

        $this->actingAs($user)
            ->get(route('reports.members.export', $query))
            ->assertOk()
            ->assertHeader('content-disposition', 'attachment; filename=members-report.csv');
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
