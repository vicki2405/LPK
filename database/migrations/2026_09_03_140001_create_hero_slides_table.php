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
        Schema::create('hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('title_jp')->nullable();
            $table->string('badge_top_label')->nullable()->default('TINGKAT KELULUSAN');
            $table->string('badge_top')->nullable()->default('98% JLPT N4 / JFT');
            $table->string('status_label')->nullable()->default('Status: Dibuka');
            $table->string('status_label_jp')->nullable()->default('募集状況: 受付中');
            $table->string('image_path');
            $table->string('salary_jpy')->nullable()->default('180k - 250k JPY');
            $table->string('salary_idr')->nullable()->default('± Rp 20 - 28 Juta/bln');
            $table->string('placement_location')->nullable()->default('Tokyo, Osaka, Aichi, dsb.');
            $table->string('placement_location_jp')->nullable()->default('東京・大阪・愛知・全国');
            $table->string('facilities')->nullable()->default('Asrama & BPJS Jepang');
            $table->string('facilities_jp')->nullable()->default('社員寮・社会保険完備');
            $table->string('cta_text')->nullable()->default('Daftar Angkatan Baru Sekarang →');
            $table->string('cta_text_jp')->nullable()->default('新期生募集に申し込む →');
            $table->string('cta_url')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hero_slides');
    }
};
