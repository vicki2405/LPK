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
        // 1. Tambahkan kolom allow_student_mode_switch ke tabel exams
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'allow_student_mode_switch')) {
                $table->boolean('allow_student_mode_switch')->default(true)->after('display_mode');
            }
        });

        // 2. Tambahkan indeks performa komposit untuk optimasi query
        Schema::table('exam_answers', function (Blueprint $table) {
            // Mempercepat query heatmap & scoring aggregations
            $table->index(['question_id', 'is_correct'], 'ea_q_correct_idx');
        });

        Schema::table('questions', function (Blueprint $table) {
            // Mempercepat filtering bank soal & filter indikator
            $table->index(['section_type', 'question_category_id'], 'q_sec_cat_idx');
            $table->index('level', 'q_level_idx');
        });

        Schema::table('exams', function (Blueprint $table) {
            // Mempercepat query jadwal ujian aktif per batch
            $table->index(['is_published', 'batch_id'], 'exams_pub_batch_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exams', function (Blueprint $table) {
            if (Schema::hasColumn('exams', 'allow_student_mode_switch')) {
                $table->dropColumn('allow_student_mode_switch');
            }
            $table->dropIndex('exams_pub_batch_idx');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->dropIndex('q_sec_cat_idx');
            $table->dropIndex('q_level_idx');
        });

        Schema::table('exam_answers', function (Blueprint $table) {
            $table->dropIndex('ea_q_correct_idx');
        });
    }
};
