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
        // Master Sektor / Bidang Kerja Jepang
        Schema::create('job_sectors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Pengolahan Makanan & Minuman"
            $table->string('name_jp')->nullable(); // Contoh: "飲食料品製造業"
            $table->string('code')->unique(); // Contoh: "FOOD_BEV"
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Master Alur Tahapan Penyaluran Trainee
        Schema::create('pipeline_stages', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Pelatihan Bahasa"
            $table->string('name_jp')->nullable(); // Contoh: "語学・マナー研修"
            $table->string('code')->unique(); // Contoh: "pelatihan"
            $table->integer('order_step')->default(1); // Urutan 1, 2, 3...
            $table->string('badge_color')->default('blue'); // blue, amber, emerald, purple, rose
            $table->string('icon')->nullable(); // Icon string
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pipeline_stages');
        Schema::dropIfExists('job_sectors');
    }
};
