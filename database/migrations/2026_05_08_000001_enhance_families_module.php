<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add new fields to families table
        Schema::table('families', function (Blueprint $table) {
            $table->string('zone')->nullable()->after('phone');
            $table->string('address')->nullable()->after('zone');
            $table->string('home_cell_group')->nullable()->after('address');
            $table->date('joined_date')->nullable()->after('home_cell_group');
            $table->text('remarks')->nullable()->after('joined_date');
        });

        // Add family_id FK to members table
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('family_id')->nullable()->after('id')
                ->constrained('families')
                ->nullOnDelete();
        });

        // Normalize gender casing in families (seeder used lowercase)
        DB::statement("UPDATE families SET gender = CONCAT(UPPER(SUBSTRING(gender,1,1)), LOWER(SUBSTRING(gender,2)))");
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropForeign(['family_id']);
            $table->dropColumn('family_id');
        });

        Schema::table('families', function (Blueprint $table) {
            $table->dropColumn(['zone', 'address', 'home_cell_group', 'joined_date', 'remarks']);
        });
    }
};
