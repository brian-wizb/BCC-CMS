<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\AuthorizationSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class PermissionMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Route::middleware(['web', 'auth', 'active', 'permission:settings.manage'])
            ->get('/test-phase-zero/settings', fn () => 'allowed');
    }

    public function test_permission_middleware_denies_user_without_permission(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create();
        $user->roles()->sync([
            Role::query()->where('key', 'pastor')->value('id'),
        ]);

        $response = $this->actingAs($user)->get('/test-phase-zero/settings');

        $response->assertForbidden();
    }

    public function test_permission_middleware_allows_user_with_permission(): void
    {
        $this->seed(AuthorizationSeeder::class);

        $user = User::factory()->create();
        $user->roles()->sync([
            Role::query()->where('key', 'system_admin')->value('id'),
        ]);

        $response = $this->actingAs($user)->get('/test-phase-zero/settings');

        $response->assertOk();
        $response->assertSeeText('allowed');
    }
}
