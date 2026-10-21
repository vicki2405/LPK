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
        Schema::dropIfExists('language_levels');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('language_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('name_jp')->nullable();
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }
};
