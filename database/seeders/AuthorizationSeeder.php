<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class AuthorizationSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $permissionKeys = config('permissions.permissions', []);
        $permissions = collect($permissionKeys)->mapWithKeys(function (string $key) {
            $permission = Permission::query()->updateOrCreate(
                ['key' => $key],
                ['label' => Str::headline(str_replace('.', ' ', $key))],
            );

            return [$key => $permission->id];
        });

        foreach (config('permissions.roles', []) as $roleKey => $definition) {
            $role = Role::query()->updateOrCreate(
                ['key' => $roleKey],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'],
                ],
            );

            $rolePermissionKeys = $definition['permissions'];
            if ($rolePermissionKeys === ['*']) {
                $role->permissions()->sync($permissions->values());
                continue;
            }

            $role->permissions()->sync(
                collect($rolePermissionKeys)
                    ->map(fn (string $permissionKey) => $permissions[$permissionKey])
                    ->filter()
                    ->values(),
            );
        }
    }
}
