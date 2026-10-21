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
        Schema::table('lessons', function (Blueprint $table) {
            $table->string('image_file')->nullable()->after('video_url');
            $table->string('audio_file')->nullable()->after('image_file');
        });

        Schema::table('vocabularies', function (Blueprint $table) {
            $table->string('image_file')->nullable()->after('audio_file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lessons', function (Blueprint $table) {
            $table->dropColumn(['image_file', 'audio_file']);
        });

        Schema::table('vocabularies', function (Blueprint $table) {
            $table->dropColumn(['image_file']);
        });
    }
};
