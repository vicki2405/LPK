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
        if (!Schema::hasColumn('question_banks', 'display_mode')) {
            Schema::table('question_banks', function (Blueprint $table) {
                $table->string('display_mode')->default('formal')->after('max_score');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('question_banks', 'display_mode')) {
            Schema::table('question_banks', function (Blueprint $table) {
                $table->dropColumn('display_mode');
            });
        }
    }
};
