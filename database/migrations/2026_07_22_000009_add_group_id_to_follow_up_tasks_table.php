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
        Schema::create('follow_up_task_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('follow_up_tasks')->cascadeOnDelete();
            $table->string('person_type');
            $table->unsignedBigInteger('person_id');
            $table->string('display_name');
            $table->string('phone')->nullable();
            $table->timestamps();

            $table->index(['task_id', 'person_type']);
            $table->index(['person_type', 'person_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follow_up_task_recipients');
    }
};
