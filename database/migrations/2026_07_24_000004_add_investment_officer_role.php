<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles') || ! Schema::hasTable('permissions')) {
            return;
        }

        $now = now();
        $roleId = DB::table('roles')->updateOrInsert(
            ['key' => 'investment_officer'],
            [
                'name' => 'Investment Officer / Manager',
                'description' => 'Manages income types and income records, with read-only access to members and tithe records.',
                'updated_at' => $now,
                'created_at' => $now,
            ],
        );

        if (! Schema::hasTable('permission_role')) {
            return;
        }

        $roleId = DB::table('roles')->where('key', 'investment_officer')->value('id');
        $permissionIds = DB::table('permissions')
            ->whereIn('key', [
                'dashboard.read',
                'dashboard.finance_kpis',
                'members.read',
                'income.read',
                'income.create',
                'income.update',
                'income.delete',
                'givings.read',
            ])
            ->pluck('id');

        foreach ($permissionIds as $permissionId) {
            DB::table('permission_role')->updateOrInsert([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        DB::table('roles')->where('key', 'investment_officer')->delete();
    }
};
