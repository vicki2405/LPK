<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat tabel Mata Pelajaran (subjects)
        if (!Schema::hasTable('subjects')) {
            Schema::create('subjects', function (Blueprint $table) {
                $table->id();
                $table->string('name'); // Contoh: "Bahasa Jepang Dasar (N5)", "Tata Bahasa (Bunpou)", "Percakapan (Kaiwa)"
                $table->string('code', 50)->unique(); // Contoh: "JPN_DASAR", "BUNPOU_N4"
                $table->text('description')->nullable();
                $table->integer('order_index')->default(0);
                $table->boolean('is_active')->default(true);
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // 2. Tambahkan kolom subject_id pada tabel exams (Paket Ujian CBT)
        if (Schema::hasTable('exams') && !Schema::hasColumn('exams', 'subject_id')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->foreignId('subject_id')->nullable()->after('created_by')->constrained('subjects')->nullOnDelete();
            });
        }

        // 3. Kaitkan paket ujian lama ke Mata Pelajaran default resmi jika ada data ujian
        $hasExams = DB::table('exams')->exists();
        if ($hasExams) {
            $existingSubject = DB::table('subjects')->where('code', 'JPN_UTAMA')->first();
            if (!$existingSubject) {
                $subjectId = DB::table('subjects')->insertGetId([
                    'name' => 'Bahasa Jepang (Nihongo)',
                    'code' => 'JPN_UTAMA',
                    'description' => 'Mata pelajaran utama Bahasa Jepang dan simulasi kompetensi kelulusan kerja.',
                    'order_index' => 1,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                $subjectId = $existingSubject->id;
            }

            DB::table('exams')->whereNull('subject_id')->update(['subject_id' => $subjectId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('exams') && Schema::hasColumn('exams', 'subject_id')) {
            Schema::table('exams', function (Blueprint $table) {
                $table->dropForeign(['subject_id']);
                $table->dropColumn('subject_id');
            });
        }

        Schema::dropIfExists('subjects');
    }
};
