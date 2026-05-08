<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leaders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->nullable()->constrained('members')->nullOnDelete();
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('role')->nullable();               // e.g. Zone Leader, Cell Group Leader, Elder
            $table->string('zone')->nullable();
            $table->string('status')->default('active');      // active, inactive
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('full_name');
            $table->index('status');
            $table->index('zone');
        });

        // Migrate follow_up_tasks.assigned_to from users → leaders (nullable, no FK yet)
        Schema::table('follow_up_tasks', function (Blueprint $table) {
            $table->dropForeign(['assigned_to']);
            $table->unsignedBigInteger('assigned_to')->nullable()->change();
            $table->foreignId('leader_id')->nullable()->after('assigned_to')
                ->constrained('leaders')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('follow_up_tasks', function (Blueprint $table) {
            $table->dropForeign(['leader_id']);
            $table->dropColumn('leader_id');
            $table->foreignId('assigned_to')->nullable()->change();
        });

        Schema::dropIfExists('leaders');
    }
};
