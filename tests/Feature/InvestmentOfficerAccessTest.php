<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvestmentOfficerAccessTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(AuthorizationSeeder::class);
    }

    public function test_role_has_expected_permissions(): void
    {
        $user = $this->investmentOfficer();

        $this->assertTrue($user->hasPermission('dashboard.read'));
        $this->assertTrue($user->hasPermission('members.read'));
        $this->assertTrue($user->hasPermission('income.create'));
        $this->assertTrue($user->hasPermission('income.update'));
        $this->assertTrue($user->hasPermission('income.delete'));
        $this->assertTrue($user->hasPermission('givings.read'));
        $this->assertFalse($user->hasPermission('members.update'));
        $this->assertFalse($user->hasPermission('givings.create'));
        $this->assertFalse($user->hasPermission('givings.update'));
        $this->assertFalse($user->hasPermission('givings.delete'));
    }

    public function test_role_receives_dedicated_dashboard(): void
    {
        $this->actingAs($this->investmentOfficer())
            ->get('/dashboard')
            ->assertOk()
            ->assertSeeText('Investment Officer Dashboard');
    }

    public function test_tithe_records_are_read_only_for_role(): void
    {
        $user = $this->investmentOfficer();

        $this->actingAs($user)->get('/givings')->assertOk();
        $this->actingAs($user)->get('/givings/create')->assertForbidden();
        $this->actingAs($user)->post('/givings', [])->assertForbidden();
    }

    private function investmentOfficer(): User
    {
        $user = User::factory()->create();
        $user->roles()->sync([
            Role::query()->where('key', 'investment_officer')->value('id'),
        ]);

        return $user->load('roles.permissions');
    }
}
