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
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->integer('violation_count')->default(0)->after('unanswered_count');
            $table->json('violation_logs')->nullable()->after('violation_count');
            $table->boolean('is_disqualified')->default(false)->after('is_passed');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropColumn(['violation_count', 'violation_logs', 'is_disqualified']);
        });
    }
};
