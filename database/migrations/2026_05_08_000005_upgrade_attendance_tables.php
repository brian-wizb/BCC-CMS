<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── services: add recurrence, mode, check-in window ──────────────
        Schema::table('services', function (Blueprint $table) {
            $table->string('recurrence_rule')->nullable()->after('description'); // none|weekly|biweekly|monthly
            $table->string('attendance_mode')->default('in_person')->after('recurrence_rule'); // in_person|online|hybrid
        });

        // ── attendance_records: add new tracking columns ──────────────────
        Schema::table('attendance_records', function (Blueprint $table) {
            // Replace free-text zone with FK (keep zone column for legacy data, add zone_id)
            $table->foreignId('zone_id')
                ->nullable()
                ->after('family_id')
                ->constrained('zones')
                ->nullOnDelete();

            // Late tracking
            $table->timestamp('check_in_time')->nullable()->after('zone_id');
            $table->string('attendance_mode')->default('in_person')->after('check_in_time'); // in_person|online|hybrid
            $table->text('notes')->nullable()->after('attendance_mode');

            // Extend status enum to include 'late'
            // attendance_status stays varchar — no enum change needed; just validate at app layer

            // Unique constraint: one record per person per service
            // We use a partial approach with a unique index on (service_id, member_id) where member_id is not null
            $table->unique(['service_id', 'member_id'], 'unique_service_member');
            $table->unique(['service_id', 'visitor_id'], 'unique_service_visitor');
            $table->unique(['service_id', 'family_id'], 'unique_service_family');
        });
    }

    public function down(): void
    {
        Schema::table('attendance_records', function (Blueprint $table) {
            $table->dropForeign(['zone_id']);
            $table->dropColumn(['zone_id', 'check_in_time', 'attendance_mode', 'notes']);
            $table->dropUnique('unique_service_member');
            $table->dropUnique('unique_service_visitor');
            $table->dropUnique('unique_service_family');
        });

        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn(['recurrence_rule', 'attendance_mode']);
        });
    }
};
