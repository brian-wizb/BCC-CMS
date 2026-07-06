<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->foreignId('partner_member_id')
                ->nullable()
                ->after('partner_name')
                ->constrained('members')
                ->nullOnDelete();
            $table->boolean('share_partner_tithe_code')
                ->default(false)
                ->after('tithe_code');
        });
    }

    public function down(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->dropConstrainedForeignId('partner_member_id');
            $table->dropColumn('share_partner_tithe_code');
        });
    }
};
