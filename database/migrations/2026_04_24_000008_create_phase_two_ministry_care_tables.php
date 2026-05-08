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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('service_type');
            $table->date('service_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('location')->nullable();
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('service_date');
            $table->index('service_type');
        });

        Schema::create('visitors', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('gender')->nullable();
            $table->string('address')->nullable();
            $table->string('invited_by')->nullable();
            $table->date('first_visit_date')->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->string('status')->default('new');
            $table->text('notes')->nullable();
            $table->foreignId('converted_member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->timestamps();

            $table->index('full_name');
            $table->index('status');
            $table->index('invited_by');
        });

        Schema::create('follow_up_tasks', function (Blueprint $table) {
            $table->id();
            $table->string('person_type');
            $table->unsignedBigInteger('person_id');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->string('task_type');
            $table->string('priority')->default('medium');
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending');
            $table->text('notes')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index(['person_type', 'person_id']);
            $table->index('status');
            $table->index('priority');
        });

        Schema::create('follow_up_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('follow_up_tasks')->cascadeOnDelete();
            $table->string('action_taken');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('task_id');
        });

        Schema::create('attendance_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('visitor_id')->nullable()->constrained('visitors')->nullOnDelete();
            $table->foreignId('family_id')->nullable()->constrained('families')->nullOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->string('zone')->nullable();
            $table->string('attendance_status')->default('present');
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recorded_at')->useCurrent();
            $table->timestamps();

            $table->index('service_id');
            $table->index('attendance_status');
        });

        Schema::create('pastoral_cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('family_id')->nullable()->constrained('families')->nullOnDelete();
            $table->string('case_type');
            $table->string('priority')->default('medium');
            $table->string('status')->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('opened_at')->useCurrent();
            $table->timestamp('closed_at')->nullable();
            $table->text('summary')->nullable();
            $table->timestamps();

            $table->index('priority');
            $table->index('status');
        });

        Schema::create('pastoral_case_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('pastoral_cases')->cascadeOnDelete();
            $table->text('note');
            $table->string('visibility')->default('private');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->useCurrent();

            $table->index('case_id');
            $table->index('visibility');
        });

        Schema::create('prayer_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->foreignId('visitor_id')->nullable()->constrained('visitors')->nullOnDelete();
            $table->string('request_type');
            $table->text('request_text');
            $table->string('visibility')->default('private');
            $table->string('status')->default('open');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('status');
            $table->index('visibility');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prayer_requests');
        Schema::dropIfExists('pastoral_case_notes');
        Schema::dropIfExists('pastoral_cases');
        Schema::dropIfExists('attendance_records');
        Schema::dropIfExists('follow_up_history');
        Schema::dropIfExists('follow_up_tasks');
        Schema::dropIfExists('visitors');
        Schema::dropIfExists('services');
    }
};
