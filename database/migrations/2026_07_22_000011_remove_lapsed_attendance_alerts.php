<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('alerts')
            ->where('alert_type', 'lapsed_attendance')
            ->delete();
    }

    public function down(): void
    {
        // Irreversible cleanup migration.
    }
};
