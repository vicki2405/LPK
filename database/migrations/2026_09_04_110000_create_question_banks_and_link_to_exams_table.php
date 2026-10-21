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
        // 1. Tabel Master Bank Soal (Question Banks / Arsip Paket Soal Permanen)
        Schema::create('question_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('subjects')->cascadeOnDelete();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->string('code')->unique();
            $table->enum('level', ['N5', 'N4', 'N3', 'N2', 'N1', 'JFT_A2'])->default('N4');
            $table->text('description')->nullable();
            $table->integer('duration_minutes')->default(60);
            $table->integer('passing_score')->default(90);
            $table->integer('max_score')->default(180);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Relasi Butir Soal ke Master Bank Soal
        Schema::create('question_bank_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_bank_id')->constrained('question_banks')->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('questions')->cascadeOnDelete();
            $table->string('section_type')->default('moji_goi');
            $table->integer('order_index')->default(1);
            $table->timestamps();

            $table->unique(['question_bank_id', 'question_id'], 'qb_q_unique');
        });

        // 3. Tambahkan question_bank_id ke tabel exams (Jadwal Ujian mengacu ke Master Bank Soal)
        Schema::table('exams', function (Blueprint $table) {
            $table->foreignId('question_bank_id')->nullable()->after('subject_id')->constrained('question_banks')->nullOnDelete();
        });

        // 4. Migrasi data yang ada secara aman (tanpa data dummy & tanpa data hilang)
        $existingExams = DB::table('exams')->get();
        foreach ($existingExams as $exam) {
            $bankCode = $exam->code . '-BNK';
            if (DB::table('question_banks')->where('code', $bankCode)->exists()) {
                $bankCode = $exam->code . '-BNK-' . uniqid();
            }

            $subjectId = $exam->subject_id;
            if (!$subjectId || !DB::table('subjects')->where('id', $subjectId)->exists()) {
                $subjectId = DB::table('subjects')->value('id') ?? 1;
            }

            $bankId = DB::table('question_banks')->insertGetId([
                'subject_id' => $subjectId,
                'created_by' => $exam->created_by,
                'title' => $exam->title,
                'code' => $bankCode,
                'level' => in_array($exam->level, ['N5', 'N4', 'N3', 'N2', 'N1', 'JFT_A2']) ? $exam->level : 'N4',
                'description' => $exam->description,
                'duration_minutes' => $exam->duration_minutes ?? 60,
                'passing_score' => $exam->passing_score ?? 90,
                'max_score' => $exam->max_score ?? 180,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Salin butir soal dari exam_questions ke question_bank_questions
            $examQuestions = DB::table('exam_questions')->where('exam_id', $exam->id)->get();
            foreach ($examQuestions as $eq) {
                DB::table('question_bank_questions')->insertOrIgnore([
                    'question_bank_id' => $bankId,
                    'question_id' => $eq->question_id,
                    'section_type' => $eq->section_type ?? 'moji_goi',
                    'order_index' => $eq->order_index ?? 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Tautkan exam ke question_bank_id
            DB::table('exams')->where('id', $exam->id)->update([
                'question_bank_id' => $bankId,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropForeign(['question_bank_id']);
            $table->dropColumn('question_bank_id');
        });

        Schema::dropIfExists('question_bank_questions');
        Schema::dropIfExists('question_banks');
    }
};
