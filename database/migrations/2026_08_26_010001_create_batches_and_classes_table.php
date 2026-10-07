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
        // Tabel Angkatan / Kelas Pelatihan
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Batch 15 - Tokutei Ginou Kaigo"
            $table->string('code')->unique(); // Contoh: "B15-KG-2026"
            $table->enum('program_type', ['tokutei_ginou', 'magang', 'gijinkoku', 'reguler'])->default('tokutei_ginou');
            $table->enum('target_level', ['N5', 'N4', 'N3'])->default('N4');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['active', 'graduated', 'archived'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // Pivot Siswa & Sensei dalam Kelas/Batch
        Schema::create('batch_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('role_in_batch', ['sensei', 'siswa'])->default('siswa');
            $table->timestamps();

            $table->unique(['batch_id', 'user_id', 'role_in_batch']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batch_user');
        Schema::dropIfExists('batches');
    }
};
