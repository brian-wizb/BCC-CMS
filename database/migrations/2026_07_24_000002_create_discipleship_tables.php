<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discipleship_participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained()->nullOnDelete();
            $table->string('external_name')->nullable();
            $table->string('external_phone')->nullable();
            $table->string('external_email')->nullable();
            $table->text('remarks')->nullable();
            $table->string('certificate_number')->nullable()->unique();
            $table->timestamp('certificate_awarded_at')->nullable();
            $table->timestamps();

            $table->unique('member_id');
            $table->index('external_name');
        });

        Schema::create('discipleship_stage_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('discipleship_participant_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('stage_number');
            $table->string('status')->default('not_started');
            $table->date('started_at')->nullable();
            $table->date('completed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['discipleship_participant_id', 'stage_number'], 'discipleship_stage_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discipleship_stage_progress');
        Schema::dropIfExists('discipleship_participants');
    }
};
