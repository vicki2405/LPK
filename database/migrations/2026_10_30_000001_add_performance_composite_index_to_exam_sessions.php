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
        if (Schema::hasTable('exam_sessions') && !Schema::hasIndex('exam_sessions', 'exam_sessions_exam_score_sub_idx')) {
            Schema::table('exam_sessions', function (Blueprint $table) {
                // Optimasi performa: Indeks komposit untuk mempercepat live leaderboard & perankingan CBT massal
                $table->index(['exam_id', 'total_score', 'submitted_at'], 'exam_sessions_exam_score_sub_idx');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('exam_sessions') && Schema::hasIndex('exam_sessions', 'exam_sessions_exam_score_sub_idx')) {
            Schema::table('exam_sessions', function (Blueprint $table) {
                $table->dropIndex('exam_sessions_exam_score_sub_idx');
            });
        }
    }
};
