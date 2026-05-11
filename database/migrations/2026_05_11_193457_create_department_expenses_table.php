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
        Schema::create('department_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('department');
            $table->string('expense');
            $table->string('payment_method');
            $table->decimal('amount', 15, 2);
            $table->date('expense_date');
            $table->string('reference_no')->nullable();
            $table->text('comment')->nullable();
            $table->string('attachment_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('department_expenses');
    }
};
