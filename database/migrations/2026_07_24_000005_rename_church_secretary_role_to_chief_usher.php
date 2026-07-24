<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        DB::table('roles')
            ->where('key', 'church_secretary')
            ->update([
                'key' => 'chief_usher',
                'name' => 'Chief Usher',
                'description' => 'Lead usher responsible for people operations and coordination across church ministries.',
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        if (! Schema::hasTable('roles')) {
            return;
        }

        DB::table('roles')
            ->where('key', 'chief_usher')
            ->update([
                'key' => 'church_secretary',
                'name' => 'Church Secretary',
                'description' => 'People management specialist with full access to people-related modules only.',
                'updated_at' => now(),
            ]);
    }
};
