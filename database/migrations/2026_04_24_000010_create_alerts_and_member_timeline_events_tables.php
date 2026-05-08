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
        Schema::create('alerts', function (Blueprint $table) {
            $table->id();
            $table->string('alert_type');
            $table->string('reference_type')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('title');
            $table->text('message');
            $table->string('severity')->default('medium');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('open');
            $table->timestamp('due_at')->nullable();
            $table->timestamps();

            $table->index('alert_type');
            $table->index('status');
            $table->index('severity');
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('member_timeline_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->cascadeOnDelete();
            $table->string('event_type');
            $table->timestamp('event_date');
            $table->string('title');
            $table->text('details')->nullable();
            $table->timestamps();

            $table->index('member_id');
            $table->index('event_date');
            $table->index('event_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_timeline_events');
        Schema::dropIfExists('alerts');
    }
};
