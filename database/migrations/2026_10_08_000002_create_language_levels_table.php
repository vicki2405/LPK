<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('language_levels', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->string('name', 255);
            $table->string('name_jp', 255)->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed data standard resmi LPK (N5 s/d N1 & JFT-Basic A2)
        DB::table('language_levels')->insert([
            [
                'code' => 'N5',
                'name' => 'JLPT N5 (Tingkat Dasar)',
                'name_jp' => 'JLPT N5 (入門・基礎)',
                'description' => 'Tingkat dasar pemahaman huruf hiragana, katakana, kanji dasar, dan percakapan harian.',
                'sort_order' => 1,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'N4',
                'name' => 'JLPT N4 & JFT-Basic A2 (Standar Kerja)',
                'name_jp' => 'JLPT N4 / JFT A2 (就労基準)',
                'description' => 'Standar minimum kemampuan bahasa Jepang untuk bekerja dan magang di Jepang.',
                'sort_order' => 2,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'N3',
                'name' => 'JLPT N3 (Tingkat Menengah)',
                'name_jp' => 'JLPT N3 (中級実用)',
                'description' => 'Pemahaman komunikasi situasi kerja spesifik dan kehidupan sehari-hari mandiri.',
                'sort_order' => 3,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'N2',
                'name' => 'JLPT N2 (Tingkat Mahir / Karir)',
                'name_jp' => 'JLPT N2 (上級・ビジネス)',
                'description' => 'Kemampuan membaca artikel surat kabar dan komunikasi bisnis tingkat lanjut.',
                'sort_order' => 4,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'N1',
                'name' => 'JLPT N1 (Tingkat Fasih / Native)',
                'name_jp' => 'JLPT N1 (最上級・ネイティブ)',
                'description' => 'Kemampuan bahasa Jepang kompleks dan berbobot secara fasih setara native speaker.',
                'sort_order' => 5,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => 'JFT_A2',
                'name' => 'JFT-Basic A2 (SSW / Tokutei Ginou)',
                'name_jp' => 'JFT-Basic A2 (特定技能)',
                'description' => 'Standar uji kompetensi komunikasi kerja untuk visa Tokutei Ginou pekerja berketerampilan spesifik.',
                'sort_order' => 6,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('language_levels');
    }
};
