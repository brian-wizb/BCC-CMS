<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Clear any orphaned assigned_to values that don't exist in leaders
        \DB::table('alerts')->update(['assigned_to' => null]);

        Schema::table('alerts', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->foreign('assigned_to')->references('id')->on('leaders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        \DB::table('alerts')->update(['assigned_to' => null]);

        Schema::table('alerts', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->foreign('assigned_to')->references('id')->on('users')->nullOnDelete();
        });
    }
};
