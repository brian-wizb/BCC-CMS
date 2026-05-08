<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_is_available(): void
    {
        $response = $this->get('/login');

        $response->assertOk();
    }

    public function test_user_can_log_in_with_username(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create([
            'username' => 'brr',
            'password' => 'secret123',
        ]);

        $user->roles()->sync([
            Role::query()->where('key', 'system_admin')->value('id'),
        ]);

        $response = $this->post('/login', [
            'username' => 'brr',
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user->fresh());
        $this->assertNotNull($user->fresh()->last_login_at);
    }

    public function test_inactive_user_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'username' => 'inactive.user',
            'password' => 'secret123',
            'status' => 'inactive',
        ]);

        $response = $this->from('/login')->post('/login', [
            'username' => $user->username,
            'password' => 'secret123',
        ]);

        $response->assertRedirect('/login');
        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }
}
