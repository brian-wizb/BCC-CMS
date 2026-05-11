<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        \DB::statement("UPDATE pastoral_cases SET assigned_to = NULL WHERE assigned_to NOT IN (SELECT id FROM leaders)");
        \DB::statement("UPDATE prayer_requests SET assigned_to = NULL WHERE assigned_to NOT IN (SELECT id FROM leaders)");

        Schema::table('pastoral_cases', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->foreign('assigned_to')->references('id')->on('leaders')->nullOnDelete();
        });

        Schema::table('prayer_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->foreign('assigned_to')->references('id')->on('leaders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        \DB::statement("UPDATE pastoral_cases SET assigned_to = NULL WHERE assigned_to NOT IN (SELECT id FROM users)");
        \DB::statement("UPDATE prayer_requests SET assigned_to = NULL WHERE assigned_to NOT IN (SELECT id FROM users)");

        Schema::table('pastoral_cases', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('prayer_requests', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });
    }
};
