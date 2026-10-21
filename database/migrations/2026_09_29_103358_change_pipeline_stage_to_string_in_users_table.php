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
        Schema::table('users', function (Blueprint $table) {
            $table->string('pipeline_stage', 100)->default('pelatihan')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('pipeline_stage', ['pelatihan', 'lulus_n4', 'matching', 'mcu', 'coe', 'visa', 'terbang'])->default('pelatihan')->change();
        });
    }
};
