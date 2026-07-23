<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pastoral_case_notes')) {
            Schema::drop('pastoral_case_notes');
        }

        if (Schema::hasTable('pastoral_cases')) {
            Schema::drop('pastoral_cases');
        }
    }

    public function down(): void
    {
        // Irreversible cleanup migration. Restore from backup if rollback is needed.
    }
};