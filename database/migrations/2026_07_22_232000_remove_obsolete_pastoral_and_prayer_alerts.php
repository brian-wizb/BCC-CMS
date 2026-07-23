<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('alerts')) {
            return;
        }

        DB::table('alerts')
            ->whereIn('alert_type', [
                'pastoral_case_overdue',
                'prayer_request_stale',
            ])
            ->delete();
    }

    public function down(): void
    {
        // Irreversible cleanup migration.
    }
};