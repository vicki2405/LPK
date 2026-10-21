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
        Schema::create('activity_galleries', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('pelatihan'); // pelatihan, cbt, mensetsu, keberangkatan, asrama
            $table->string('image_path');
            $table->text('description')->nullable();
            $table->date('activity_date')->nullable();
            $table->integer('order_index')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_galleries');
    }
};
