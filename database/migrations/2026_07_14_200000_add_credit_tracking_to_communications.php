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
        Schema::table('communications', function (Blueprint $table) {
            $table->unsignedInteger('estimated_sms_count')->default(0)->after('status');
            $table->unsignedInteger('actual_sms_count')->default(0)->after('estimated_sms_count');
        });

        Schema::create('communication_credit_settings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('credits_purchased_total')->default(0);
            $table->unsignedInteger('low_balance_threshold')->default(100);
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('updated_by')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_credit_settings');

        Schema::table('communications', function (Blueprint $table) {
            $table->dropColumn(['estimated_sms_count', 'actual_sms_count']);
        });
    }
};
