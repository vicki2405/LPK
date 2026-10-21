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
        Schema::create('kanjis', function (Blueprint $table) {
            $table->id();
            $table->string('kanji', 50);
            $table->string('hiragana', 100);
            $table->string('meaning_id', 255);
            $table->string('romaji', 100)->nullable();
            $table->string('onyomi', 100)->nullable();
            $table->string('kunyomi', 100)->nullable();
            $table->string('level', 20)->default('N5'); // N5, N4, N3, etc.
            $table->integer('stroke_count')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('kanji');
            $table->index('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kanjis');
    }
};
