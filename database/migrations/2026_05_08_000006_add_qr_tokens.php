<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('members', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique()->after('remarks');
        });

        Schema::table('visitors', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique()->after('notes');
        });

        Schema::table('leaders', function (Blueprint $table) {
            $table->string('qr_token', 64)->nullable()->unique()->after('notes');
        });

        // Backfill tokens for all existing records
        foreach (['members', 'visitors', 'leaders'] as $table) {
            $ids = DB::table($table)->whereNull('qr_token')->pluck('id');
            foreach ($ids as $id) {
                DB::table($table)->where('id', $id)
                    ->update(['qr_token' => Str::random(32)]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('members',  fn (Blueprint $t) => $t->dropColumn('qr_token'));
        Schema::table('visitors', fn (Blueprint $t) => $t->dropColumn('qr_token'));
        Schema::table('leaders',  fn (Blueprint $t) => $t->dropColumn('qr_token'));
    }
};
