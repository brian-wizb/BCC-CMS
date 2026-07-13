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
            $table->string('sms_delivery_status')->nullable()->after('attachment');
            $table->text('sms_provider_response')->nullable()->after('sms_delivery_status');
            $table->timestamp('sms_sent_at')->nullable()->after('sms_provider_response');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['sms_delivery_status', 'sms_provider_response', 'sms_sent_at']);
        });
    }
};
