<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenditures', function (Blueprint $table) {
            // rename old columns to match live system
            if (Schema::hasColumn('expenditures', 'date') && !Schema::hasColumn('expenditures', 'expense_date')) {
                $table->renameColumn('date', 'expense_date');
            }
            if (Schema::hasColumn('expenditures', 'description') && !Schema::hasColumn('expenditures', 'expense_category')) {
                $table->renameColumn('description', 'expense_category');
            }
            if (Schema::hasColumn('expenditures', 'attachment') && !Schema::hasColumn('expenditures', 'attachment_url')) {
                $table->renameColumn('attachment', 'attachment_url');
            }
            if (Schema::hasColumn('expenditures', 'notes') && !Schema::hasColumn('expenditures', 'comment')) {
                $table->renameColumn('notes', 'comment');
            }
            if (!Schema::hasColumn('expenditures', 'payment_method')) {
                $table->string('payment_method')->default('Cash')->after('expense_category');
            }
            if (!Schema::hasColumn('expenditures', 'reference_no')) {
                $table->string('reference_no')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('expenditures', 'status')) {
                $table->string('status')->default('Paid')->after('attachment_url');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenditures', function (Blueprint $table) {
            if (Schema::hasColumn('expenditures', 'expense_date')) {
                $table->renameColumn('expense_date', 'date');
            }
            if (Schema::hasColumn('expenditures', 'expense_category')) {
                $table->renameColumn('expense_category', 'description');
            }
            if (Schema::hasColumn('expenditures', 'attachment_url')) {
                $table->renameColumn('attachment_url', 'attachment');
            }
            if (Schema::hasColumn('expenditures', 'comment')) {
                $table->renameColumn('comment', 'notes');
            }
            $table->dropColumnIfExists('payment_method');
            $table->dropColumnIfExists('reference_no');
            $table->dropColumnIfExists('status');
        });
    }
};
