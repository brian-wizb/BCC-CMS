<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_records') && Schema::hasColumn('attendance_records', 'family_id')) {
            try {
                Schema::table('attendance_records', function (Blueprint $table) {
                    $table->dropUnique('unique_service_family');
                });
            } catch (Throwable $e) {
                // Index may not exist in some environments.
            }

            try {
                Schema::table('attendance_records', function (Blueprint $table) {
                    $table->dropForeign(['family_id']);
                });
            } catch (Throwable $e) {
                // Foreign key may already be absent.
            }

            Schema::table('attendance_records', function (Blueprint $table) {
                $table->dropColumn('family_id');
            });
        }

        if (Schema::hasTable('members') && Schema::hasColumn('members', 'family_id')) {
            try {
                Schema::table('members', function (Blueprint $table) {
                    $table->dropForeign(['family_id']);
                });
            } catch (Throwable $e) {
                // Foreign key may already be absent.
            }

            Schema::table('members', function (Blueprint $table) {
                $table->dropColumn('family_id');
            });
        }

        if (Schema::hasTable('pastoral_cases') && Schema::hasColumn('pastoral_cases', 'family_id')) {
            try {
                Schema::table('pastoral_cases', function (Blueprint $table) {
                    $table->dropForeign(['family_id']);
                });
            } catch (Throwable $e) {
                // Foreign key may already be absent.
            }

            Schema::table('pastoral_cases', function (Blueprint $table) {
                $table->dropColumn('family_id');
            });
        }

        if (Schema::hasTable('families')) {
            Schema::drop('families');
        }
    }

    public function down(): void
    {
        // Irreversible cleanup migration. Restore from backup if rollback is needed.
    }
};