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
        // 1. Topics Table (Wadah / Kelompok Materi per Level)
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20)->default('vocabulary'); // 'vocabulary' or 'kanji'
            $table->string('level', 20)->default('N5'); // 'N5', 'N4', etc.
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['type', 'level']);
        });

        // 2. Pivot Table topic_vocabulary (Many-to-Many: 1 kata bisa di > 1 topik)
        Schema::create('topic_vocabulary', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->cascadeOnDelete();
            $table->foreignId('vocabulary_id')->constrained('vocabularies')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['topic_id', 'vocabulary_id']);
        });

        // 3. Pivot Table kanji_topic (Many-to-Many: 1 kanji bisa di > 1 topik)
        Schema::create('kanji_topic', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->constrained('topics')->cascadeOnDelete();
            $table->foreignId('kanji_id')->constrained('kanjis')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['topic_id', 'kanji_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanji_topic');
        Schema::dropIfExists('topic_vocabulary');
        Schema::dropIfExists('topics');
    }
};