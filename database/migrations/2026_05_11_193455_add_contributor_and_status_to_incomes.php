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
        Schema::table('incomes', function (Blueprint $table) {
            $table->string('contributor_name')->nullable()->after('member_id');
            $table->string('contributor_contacts')->nullable()->after('contributor_name');
            $table->string('contributor_address')->nullable()->after('contributor_contacts');
            $table->string('status')->default('Received')->after('attachment_url');
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropColumn(['contributor_name', 'contributor_contacts', 'contributor_address', 'status']);
        });
    }
};
