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
        Schema::table('pledges', function (Blueprint $table) {
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete()->after('id');
            $table->string('pledger_phone', 30)->nullable()->after('pledger_name');
            $table->string('pledge_type', 20)->default('Individual')->after('pledger_phone');
            $table->date('due_date')->nullable()->after('pledge_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pledges', function (Blueprint $table) {
            $table->dropForeign(['member_id']);
            $table->dropColumn(['member_id', 'pledger_phone', 'pledge_type', 'due_date']);
        });
    }
};
