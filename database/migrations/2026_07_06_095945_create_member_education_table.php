<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('universities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('country')->default('Tanzania');
            $table->enum('type', ['local', 'diaspora', 'other'])->default('local');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('universities');
    }
};
