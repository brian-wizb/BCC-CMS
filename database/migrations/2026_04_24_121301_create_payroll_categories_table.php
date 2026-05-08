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
        Schema::create('payroll_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type'); // Addition or Deduction
            $table->string('charge_in'); // Amount or Percent
            $table->decimal('charge', 12, 2)->default(0);
            $table->boolean('deduct_after_paye')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_categories');
    }
};
