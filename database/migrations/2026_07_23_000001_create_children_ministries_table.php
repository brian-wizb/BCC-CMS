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
        Schema::create('children_ministries', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('surname');
            $table->date('date_of_birth')->nullable();
            $table->string('sex');
            $table->string('parent_name');
            $table->string('parent_contact')->nullable();
            $table->unsignedBigInteger('parent_member_id')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('parent_member_id')
                ->references('id')
                ->on('members')
                ->nullOnDelete();

            $table->index('first_name');
            $table->index('parent_name');
            $table->index('parent_contact');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('children_ministries');
    }
};
