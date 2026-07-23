<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only run if permissions table exists (it's created in a prior migration)
        if (! DB::getSchemaBuilder()->hasTable('permissions')) {
            return;
        }

        // Delete permission records for entirely removed modules
        $obsoleteKeys = [
            'families.read',
            'families.create',
            'families.update',
            'families.delete',
            'families.export',
            'families.import',
            'pastoral_care.read',
            'pastoral_care.create',
            'pastoral_care.update',
            'pastoral_care.delete',
            'pastoral_care.notes.create',
            'prayer_requests.read',
            'prayer_requests.create',
            'prayer_requests.update',
            'prayer_requests.delete',
            'events.read',
            'events.create',
            'events.update',
            'events.delete',
            'volunteers.read',
            'volunteers.create',
            'volunteers.update',
            'volunteers.delete',
            'payroll.read',
            'payroll.create',
            'payroll.update',
            'payroll.delete',
            'payroll.export',
            'payroll.approve',
            'payroll.categories.manage',
        ];

        foreach ($obsoleteKeys as $key) {
            DB::table('permissions')->where('key', $key)->delete();
        }

        // Clean up any role_permission associations with non-existent permissions
        if (DB::getSchemaBuilder()->hasTable('permission_role')) {
            DB::table('permission_role')
                ->whereNotIn('permission_id', DB::table('permissions')->select('id'))
                ->delete();
        }

        if (DB::getSchemaBuilder()->hasTable('role_permission')) {
            DB::table('role_permission')
                ->whereNotIn('permission_id', DB::table('permissions')->select('id'))
                ->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // This is a data cleanup migration. Reversal is not practical.
        // If needed, restore from database backup.
    }
};
