<?php

namespace Tests\Feature;

use App\Models\Member;
use App\Models\Department;
use App\Models\Role;
use App\Models\User;
use App\Models\Zone;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PhaseOneManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_system_admin_can_create_a_user_from_management_page(): void
    {
        $admin = $this->actingAsRole('system_admin');

        $response = $this->actingAs($admin)->post(route('users.store'), [
            'username' => 'memberadmin',
            'full_name' => 'Member Administrator',
            'email' => 'memberadmin@example.com',
            'role' => 'member_admin',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('users.index'));

        $this->assertDatabaseHas('users', [
            'username' => 'memberadmin',
            'full_name' => 'Member Administrator',
            'status' => 'active',
        ]);
    }

    public function test_member_admin_can_create_member(): void
    {
        $user = $this->actingAsRole('church_secretary');

        $response = $this->actingAs($user)->post(route('members.store'), [
            'full_name' => 'Jane Doe',
            'gender' => 'Female',
            'phone' => '0712345678',
            'zone' => 'North Zone',
            'residency' => 'Dar es Salaam',
        ]);

        $response->assertRedirect(route('members.index'));

        $this->assertDatabaseHas('members', [
            'full_name' => 'Jane Doe',
            'gender' => 'Female',
            'zone' => 'North Zone',
        ]);
    }

    public function test_accountant_cannot_access_user_management(): void
    {
        $user = $this->actingAsRole('accountant');

        $response = $this->actingAs($user)->get(route('users.index'));

        $response->assertForbidden();
    }

    public function test_member_admin_can_create_department_and_assign_member(): void
    {
        $user = $this->actingAsRole('church_secretary');
        $leader = User::factory()->create();
        $member = Member::query()->create([
            'full_name' => 'Department Member',
            'gender' => 'Female',
        ]);

        $createResponse = $this->actingAs($user)->post(route('departments.store'), [
            'name' => 'Choir',
            'leader_id' => $leader->id,
            'description' => 'Music ministry',
            'status' => 'active',
        ]);

        $department = Department::query()->where('name', 'Choir')->firstOrFail();

        $createResponse->assertRedirect(route('departments.show', $department));

        $assignResponse = $this->actingAs($user)->post(route('departments.members.store', $department), [
            'member_id' => $member->id,
            'role' => 'leader',
            'status' => 'active',
        ]);

        $assignResponse->assertRedirect(route('departments.show', $department));

        $this->assertDatabaseHas('departments', [
            'name' => 'Choir',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('department_members', [
            'department_id' => $department->id,
            'member_id' => $member->id,
            'role' => 'leader',
        ]);
    }

    public function test_member_admin_can_create_zone_and_assign_member(): void
    {
        $user = $this->actingAsRole('church_secretary');
        $leader = User::factory()->create();
        $member = Member::query()->create([
            'full_name' => 'Zone Member',
            'gender' => 'Male',
        ]);

        $createResponse = $this->actingAs($user)->post(route('zones.store'), [
            'name' => 'North Zone',
            'leader_id' => $leader->id,
            'description' => 'Northern community',
            'status' => 'active',
        ]);

        $zone = Zone::query()->where('name', 'North Zone')->firstOrFail();

        $createResponse->assertRedirect(route('zones.show', $zone));

        $assignResponse = $this->actingAs($user)->post(route('zones.members.store', $zone), [
            'member_id' => $member->id,
            'status' => 'active',
        ]);

        $assignResponse->assertRedirect(route('zones.show', $zone));

        $this->assertDatabaseHas('zones', [
            'name' => 'North Zone',
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('zone_members', [
            'zone_id' => $zone->id,
            'member_id' => $member->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('members', [
            'id' => $member->id,
            'zone' => 'North Zone',
        ]);
    }

    public function test_accountant_cannot_access_department_creation(): void
    {
        $user = $this->actingAsRole('accountant');

        $response = $this->actingAs($user)->get(route('departments.create'));

        $response->assertForbidden();
    }

    public function test_members_index_lists_existing_members(): void
    {
        $user = $this->actingAsRole('church_secretary');
        Member::query()->create([
            'full_name' => 'Existing Member',
            'gender' => 'Male',
        ]);

        $response = $this->actingAs($user)->get(route('members.index'));

        $response->assertOk();
        $response->assertSeeText('Existing Member');
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
