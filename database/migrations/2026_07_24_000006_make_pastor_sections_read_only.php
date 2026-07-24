<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles') || ! Schema::hasTable('permissions') || ! Schema::hasTable('permission_role')) {
            return;
        }

        $roleId = DB::table('roles')->where('key', 'pastor')->value('id');
        if (! $roleId) {
            return;
        }

        $permissionKeys = [
            'dashboard.read',
            'dashboard.finance_kpis',
            'dashboard.membership_kpis',
            'reports.read',
            'reports.financial.read',
            'reports.membership.read',
            'members.read',
            'departments.read',
            'zones.read',
            'groups.read',
            'visitors.read',
            'children_ministry.read',
            'follow_up.read',
            'leaders.read',
            'member_timeline.read',
            'discipleship.read',
            'attendance.read',
            'attendance.reports.read',
            'alerts.read',
            'communications.read',
            'income.read',
            'expenditures.read',
            'givings.read',
            'campaigns.read',
            'pledges.read',
            'pledge_payments.read',
            'audit_logs.read',
        ];

        $permissionIds = DB::table('permissions')
            ->whereIn('key', $permissionKeys)
            ->pluck('id');

        DB::table('permission_role')->where('role_id', $roleId)->delete();
        DB::table('permission_role')->insert(
            $permissionIds->map(fn ($permissionId) => [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ])->all(),
        );

        DB::table('roles')->where('id', $roleId)->update([
            'description' => 'Senior leadership with read-only visibility across people, ministry, and finance.',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        // Do not automatically restore write access.
    }
};
