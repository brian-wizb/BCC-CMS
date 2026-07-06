<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('employment_status')->nullable()->after('residency');
            $table->boolean('is_university_student')->default(false)->after('employment_status');
            $table->foreignId('university_id')->nullable()->constrained('universities')->nullOnDelete()->after('is_university_student');
            $table->date('university_start_date')->nullable()->after('university_id');
            $table->date('university_end_date')->nullable()->after('university_start_date');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('university_id');
            $table->dropColumn(['employment_status', 'is_university_student', 'university_start_date', 'university_end_date']);
        });
    }
};
