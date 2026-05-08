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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('tithe_code')->nullable();
            $table->string('gender');
            $table->string('zone')->nullable();
            $table->string('residency')->nullable();
            $table->string('marital_status')->nullable();
            $table->string('profile_pic')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('partner_name')->nullable();
            $table->date('married_date')->nullable();
            $table->boolean('is_born_again')->default(false);
            $table->date('born_again_date')->nullable();
            $table->boolean('is_baptized')->default(false);
            $table->date('baptized_date')->nullable();
            $table->boolean('holy_spirit_baptised')->default(false);
            $table->date('membership_date')->nullable();
            $table->string('member_code')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->index('full_name');
            $table->index('phone');
            $table->index('tithe_code');
            $table->index('zone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('members');
    }
};
