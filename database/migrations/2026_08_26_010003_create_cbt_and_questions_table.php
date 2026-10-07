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
        // 1. Kategori Soal (Moji-Goi, Bunpou, Dokkai, Choukai)
        Schema::create('question_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: "Moji & Goi (Huruf & Kosakata)", "Bunpou & Dokkai", "Choukai (Listening)"
            $table->enum('section_type', ['moji_goi', 'bunpou', 'dokkai', 'choukai'])->default('moji_goi');
            $table->enum('level', ['N5', 'N4', 'N3', 'JFT_A2'])->default('N4');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Bank Soal (Questions)
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Sensei pembuat soal
            $table->foreignId('question_category_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('chapter_id')->nullable()->constrained()->nullOnDelete(); // Kaitan ke bab materi jika kuis bab
            $table->enum('level', ['N5', 'N4', 'N3', 'JFT_A2'])->default('N4');
            $table->enum('section_type', ['moji_goi', 'bunpou', 'dokkai', 'choukai'])->default('moji_goi');
            
            // Konten Soal
            $table->text('instruction')->nullable(); // Petunjuk soal (misal: "_____の ことばは どう かきますか。")
            $table->longText('question_text'); // Teks soal, bisa memuat <ruby>漢字<rt>かんじ</rt></ruby> atau kata bintang ★
            $table->longText('reading_passage')->nullable(); // Teks bacaan panjang untuk Dokkai (Wacana, Jadwal/Brosur)
            $table->string('image_url')->nullable(); // Gambar ilustrasi / situasi soal
            $table->string('audio_url')->nullable(); // File audio MP3 untuk soal Choukai
            $table->integer('audio_play_limit')->default(1); // Maksimal berapa kali audio boleh diputar (1x untuk simulasi N4)
            
            // Penjelasan / Pembahasan (Kaisetsu)
            $table->longText('explanation')->nullable(); // Penjelasan detail tata bahasa & arti untuk review siswa
            $table->decimal('score_points', 5, 2)->default(1.00); // Bobot nilai soal
            
            $table->timestamps();
        });

        // 3. Opsi Jawaban Soal (Multiple Choice Options)
        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->string('option_key', 5); // 1, 2, 3, 4 atau A, B, C, D
            $table->text('option_text')->nullable(); // Teks opsi (dengan ruby furigana)
            $table->string('option_image')->nullable(); // Opsi berupa gambar jika ada
            $table->boolean('is_correct')->default(false); // Penanda jawaban benar
            $table->timestamps();
        });

        // 4. Paket Ujian CBT (Exams / Simulasi N4 & Kuis)
        Schema::create('exams', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // Sensei
            $table->foreignId('batch_id')->nullable()->constrained()->nullOnDelete(); // null jika untuk semua siswa, atau terikat ke kelas tertentu
            $table->string('title'); // Contoh: "Simulasi Ujian JLPT N4 - Paket Tryout 01"
            $table->string('code')->unique(); // Kode unik ujian
            $table->enum('exam_type', ['jlpt_simulation', 'jft_simulation', 'chapter_quiz', 'daily_test'])->default('jlpt_simulation');
            $table->enum('level', ['N5', 'N4', 'N3', 'JFT_A2'])->default('N4');
            $table->text('description')->nullable();
            
            // Pengaturan Waktu & Standar Kelulusan
            $table->integer('duration_minutes')->default(60); // Durasi ujian total (menit)
            $table->integer('passing_score')->default(90); // Skor minimal lulus (misal 90 dari 180 untuk N4)
            $table->integer('max_score')->default(180); // Skor maksimal total
            $table->integer('max_attempts')->default(1); // Berapa kali boleh mencoba
            
            // Jadwal & Akses
            $table->dateTime('start_time')->nullable(); // Kapan ujian mulai dibuka
            $table->dateTime('end_time')->nullable(); // Batas akhir ujian ditutup
            $table->string('access_token')->nullable(); // Token masuk ujian (opsional)
            $table->boolean('is_randomized')->default(false); // Acak urutan soal
            $table->boolean('allow_review_immediately')->default(true); // Izinkan siswa melihat pembahasan setelah submit
            $table->boolean('is_published')->default(false); // Status aktif/draft
            
            $table->timestamps();
        });

        // 5. Relasi Soal dalam Paket Ujian (Exam Questions Pivot)
        Schema::create('exam_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->enum('section_type', ['moji_goi', 'bunpou', 'dokkai', 'choukai'])->default('moji_goi');
            $table->integer('order_index')->default(1);
            $table->timestamps();

            $table->unique(['exam_id', 'question_id']);
        });

        // 6. Sesi Pengerjaan Ujian Siswa (Exam Sessions)
        Schema::create('exam_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Siswa
            $table->integer('attempt_number')->default(1);
            
            // Tracking Waktu
            $table->dateTime('started_at');
            $table->dateTime('expires_at');
            $table->dateTime('submitted_at')->nullable();
            
            // Hasil Penilaian (Scoring & Sectional Breakdown)
            $table->decimal('total_score', 6, 2)->default(0.00);
            $table->decimal('moji_goi_score', 6, 2)->default(0.00);
            $table->decimal('bunpou_dokkai_score', 6, 2)->default(0.00);
            $table->decimal('choukai_score', 6, 2)->default(0.00);
            $table->integer('correct_answers_count')->default(0);
            $table->integer('wrong_answers_count')->default(0);
            $table->integer('unanswered_count')->default(0);
            
            // Status Kelulusan Standar N4
            $table->boolean('is_passed')->default(false); // 合格 (Lulus) atau 不合格 (Tidak Lulus)
            $table->enum('status', ['in_progress', 'submitted', 'timed_out', 'cancelled'])->default('in_progress');
            
            $table->timestamps();

            $table->index(['user_id', 'exam_id', 'status']);
        });

        // 7. Riwayat Jawaban Siswa per Soal (Exam Answers)
        Schema::create('exam_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_option_id')->nullable()->constrained()->nullOnDelete(); // Opsi yang dipilih siswa
            $table->boolean('is_doubtful')->default(false); // Penanda ragu-ragu
            $table->boolean('is_correct')->default(false); // Hasil koreksi otomatis
            $table->decimal('score_earned', 5, 2)->default(0.00);
            $table->timestamp('answered_at')->nullable();
            $table->timestamps();

            $table->unique(['exam_session_id', 'question_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_answers');
        Schema::dropIfExists('exam_sessions');
        Schema::dropIfExists('exam_questions');
        Schema::dropIfExists('exams');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('question_categories');
    }
};
