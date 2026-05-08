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
        Schema::table('pledge_payments', function (Blueprint $table) {
            $table->foreignId('campaign_id')->nullable()->constrained('campaigns');
            $table->string('phone')->nullable();
            $table->string('invoice_number')->nullable();
            $table->string('attachment')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pledge_payments', function (Blueprint $table) {
            $table->dropForeign(['campaign_id']);
            $table->dropColumn(['campaign_id', 'phone', 'invoice_number', 'attachment']);
        });
    }
};
