<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! DB::getSchemaBuilder()->hasTable('permissions') || ! DB::getSchemaBuilder()->hasTable('roles')) {
            return;
        }

        $now = now();
        $permissions = [
            'children_ministry.read' => 'Children Ministry Read',
            'children_ministry.create' => 'Children Ministry Create',
            'children_ministry.update' => 'Children Ministry Update',
            'children_ministry.delete' => 'Children Ministry Delete',
            'children_ministry.export' => 'Children Ministry Export',
            'discipleship.read' => 'Discipleship Read',
            'discipleship.create' => 'Discipleship Create',
            'discipleship.update' => 'Discipleship Update',
            'discipleship.award' => 'Discipleship Award',
        ];

        foreach ($permissions as $key => $label) {
            $query = DB::table('permissions')->where('key', $key);
            if ($query->exists()) {
                $query->update(['label' => $label, 'updated_at' => $now]);
            } else {
                DB::table('permissions')->insert(['key' => $key, 'label' => $label, 'created_at' => $now, 'updated_at' => $now]);
            }
        }

        if (! DB::getSchemaBuilder()->hasTable('permission_role')) {
            return;
        }

        $rolePermissions = [
            'pastor' => [
                'children_ministry.read', 'children_ministry.create', 'children_ministry.update', 'children_ministry.export',
                'discipleship.read', 'discipleship.create', 'discipleship.update', 'discipleship.award',
            ],
            'chief_usher' => [
                'children_ministry.read', 'children_ministry.create', 'children_ministry.update', 'children_ministry.delete', 'children_ministry.export',
                'discipleship.read', 'discipleship.create', 'discipleship.update', 'discipleship.award',
            ],
        ];

        foreach ($rolePermissions as $roleKey => $permissionKeys) {
            $roleId = DB::table('roles')->where('key', $roleKey)->value('id');
            if (! $roleId) {
                continue;
            }

            $permissionIds = DB::table('permissions')->whereIn('key', $permissionKeys)->pluck('id');
            foreach ($permissionIds as $permissionId) {
                $grant = DB::table('permission_role')->where('role_id', $roleId)->where('permission_id', $permissionId);
                if ($grant->exists()) {
                    $grant->update(['updated_at' => $now]);
                } else {
                    DB::table('permission_role')->insert(['role_id' => $roleId, 'permission_id' => $permissionId, 'created_at' => $now, 'updated_at' => $now]);
                }
            }
        }
    }

    public function down(): void
    {
        // Permission grants are operational data and should not be automatically revoked.
    }
};
