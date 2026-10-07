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
        // 1. Kursus / Level Kurikulum (N5, N4, Tokutei Ginou Kaigo, dll.)
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title'); // Contoh: "Bahasa Jepang Standar N4 (Minna no Nihongo II)"
            $table->string('slug')->unique();
            $table->enum('level', ['N5', 'N4', 'N3', 'SSW_KAIGO', 'SSW_FOOD', 'SSW_AGRICULTURE', 'GENERAL'])->default('N4');
            $table->text('description')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('is_published')->default(false);
            $table->integer('order_index')->default(1);
            $table->timestamps();
        });

        // 2. Bab Pelajaran (Dai 1-50 Ka / Chou)
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->integer('chapter_number'); // Contoh: 26 (Bab 26)
            $table->string('title'); // Contoh: "Bab 26: Bentuk ~ndesu (~んです)"
            $table->text('description')->nullable();
            $table->integer('order_index')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 3. Sub-Materi Pelajaran (Tata Bahasa, Teks Bacaan, Video Pembelajaran, PDF)
        Schema::create('lessons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->string('title'); // Contoh: "Tata Bahasa: Pola ~ndesu ga, ~te itadakemasen ka"
            $table->enum('content_type', ['text_grammar', 'video', 'pdf_handout', 'culture'])->default('text_grammar');
            $table->longText('content_body')->nullable(); // Dukungan format HTML + <ruby> furigana
            $table->string('video_url')->nullable(); // Embed video YouTube / link MP4
            $table->string('pdf_file')->nullable(); // File PDF yang diunggah Sensei
            $table->integer('duration_minutes')->nullable();
            $table->integer('order_index')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 4. Kosakata (Kotoba & Flashcards Generator)
        Schema::create('vocabularies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chapter_id')->constrained()->cascadeOnDelete();
            $table->string('kanji')->nullable(); // Kanji (misal: 病院)
            $table->string('hiragana'); // Hiragana (misal: びょういん)
            $table->string('romaji')->nullable(); // Romaji (misal: byouin)
            $table->string('meaning_id'); // Arti Indonesia (misal: Rumah Sakit)
            $table->string('word_type')->default('noun'); // noun, verb_1, verb_2, verb_3, i_adj, na_adj, adverb, particle, expression
            $table->string('audio_file')->nullable(); // File audio pelafalan MP3
            $table->text('example_sentence_jp')->nullable(); // Contoh kalimat Jepang
            $table->text('example_sentence_id')->nullable(); // Arti kalimat Indonesia
            $table->integer('order_index')->default(1);
            $table->timestamps();
        });

        // 5. Progress Belajar Siswa per Bab (Checklist / Mark as Completed)
        Schema::create('lesson_completions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at')->useCurrent();

            $table->unique(['user_id', 'lesson_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lesson_completions');
        Schema::dropIfExists('vocabularies');
        Schema::dropIfExists('lessons');
        Schema::dropIfExists('chapters');
        Schema::dropIfExists('courses');
    }
};
