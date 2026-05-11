<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Nullify any leader_ids that don't match a leaders row
        \DB::statement("UPDATE departments SET leader_id = NULL WHERE leader_id NOT IN (SELECT id FROM leaders)");
        \DB::statement("UPDATE zones SET leader_id = NULL WHERE leader_id NOT IN (SELECT id FROM leaders)");

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->foreign('leader_id')->references('id')->on('leaders')->nullOnDelete();
        });

        Schema::table('zones', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->foreign('leader_id')->references('id')->on('leaders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        // Nullify any leader_ids that don't match a users row
        \DB::statement("UPDATE departments SET leader_id = NULL WHERE leader_id NOT IN (SELECT id FROM users)");
        \DB::statement("UPDATE zones SET leader_id = NULL WHERE leader_id NOT IN (SELECT id FROM users)");

        Schema::table('departments', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->foreign('leader_id')->references('id')->on('users')->nullOnDelete();
        });

        Schema::table('zones', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->foreign('leader_id')->references('id')->on('users')->nullOnDelete();
        });
    }
};
