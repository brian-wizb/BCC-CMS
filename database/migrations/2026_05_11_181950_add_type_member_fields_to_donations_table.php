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
        Schema::table('donations', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete()->after('id');
            $table->string('type')->nullable()->after('member_id');          // Tithe [Zaka] / Sadaka ya Shukrani / Mission / Other
            $table->string('tithe_code')->nullable()->after('type');
            $table->string('reference')->nullable()->after('tithe_code');
            $table->string('attachment')->nullable()->after('notes');         // stored file path
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn(['member_id', 'type', 'tithe_code', 'reference', 'attachment']);
        });
    }
};
