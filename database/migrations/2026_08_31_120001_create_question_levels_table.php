<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Membuat tabel question_levels yang fleksibel menggantikan ENUM hardcoded.
     */
    public function up(): void
    {
        // 1. Buat tabel question_levels (pengganti ENUM level hardcoded)
        Schema::create('question_levels', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();        // Contoh: N5, N4, BASIC, LEVEL_1
            $table->string('name');                      // Contoh: JLPT N5 (Dasar), Basic Level
            $table->text('description')->nullable();
            $table->integer('order_index')->default(0); // Urutan tampil
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Seed data default (bisa diedit/dihapus Sensei nanti)
        DB::table('question_levels')->insert([
            ['code' => 'N5',     'name' => 'JLPT N5 — Dasar Percakapan',        'description' => 'Kemampuan dasar bahasa Jepang.', 'order_index' => 1, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'N4',     'name' => 'JLPT N4 — Standar Kerja',           'description' => 'Standar minimum bekerja di Jepang.', 'order_index' => 2, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'N3',     'name' => 'JLPT N3 — Menengah',                'description' => 'Kemampuan komunikasi sehari-hari.', 'order_index' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'N2',     'name' => 'JLPT N2 — Lanjutan',                'description' => 'Kemampuan profesional.', 'order_index' => 4, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'N1',     'name' => 'JLPT N1 — Mahir',                   'description' => 'Kemampuan native-level.', 'order_index' => 5, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'JFT_A2', 'name' => 'JFT-Basic A2 — SSW / Tokutei',     'description' => 'Tes khusus program Tokutei Ginou (SSW).', 'order_index' => 6, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // 3. Tambah kolom level_code (string) ke questions sebagai pengganti ENUM
        Schema::table('questions', function (Blueprint $table) {
            $table->string('level_code', 20)->nullable()->after('level');
        });

        // 4. Migrate data lama ke kolom baru
        DB::statement('UPDATE questions SET level_code = level');

        // 5. Tambah kolom level_code ke question_categories
        Schema::table('question_categories', function (Blueprint $table) {
            $table->string('level_code', 20)->nullable()->after('level');
            $table->string('code', 30)->nullable()->after('name'); // kode singkat kategori
        });

        DB::statement('UPDATE question_categories SET level_code = level');

        // 6. Tambah kolom description jika belum ada di question_categories (sudah ada)
        // Tidak perlu, sudah ada dari migration awal
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('question_categories', function (Blueprint $table) {
            $table->dropColumn(['level_code', 'code']);
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('level_code');
        });

        Schema::dropIfExists('question_levels');
    }
};
