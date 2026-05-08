<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class SystemUserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $password = env('BCC_DEFAULT_ADMIN_PASSWORD', 'ChangeMe123!');

        $attributes = [
            'full_name' => 'BCC System Administrator',
            'email' => 'admin@bcc.local',
            'phone' => null,
            'password' => Hash::make($password),
            'status' => 'active',
            'last_login_at' => null,
        ];

        if (Schema::hasColumn('users', 'name')) {
            $attributes['name'] = 'BCC System Administrator';
        }

        $admin = User::query()->updateOrCreate(
            ['username' => 'brr'],
            $attributes,
        );

        $systemAdminRole = Role::query()->where('key', 'system_admin')->first();

        if ($systemAdminRole) {
            // Assign all permissions to the system_admin role
            $allPermissionIds = \App\Models\Permission::pluck('id')->all();
            $systemAdminRole->permissions()->sync($allPermissionIds);
            $admin->roles()->sync([$systemAdminRole->id]);
        }
    }
}
