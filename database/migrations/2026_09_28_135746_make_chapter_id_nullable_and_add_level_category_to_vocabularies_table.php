<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('vocabularies', function (Blueprint $table) {
            $table->string('level', 50)->default('N5')->after('id')->index();
            $table->string('category', 100)->nullable()->after('level')->index();
            $table->unsignedBigInteger('chapter_id')->nullable()->change();
        });

        // Backfill level dari chapters & courses yang sudah ada
        DB::statement("
            UPDATE vocabularies v
            LEFT JOIN chapters c ON v.chapter_id = c.id
            LEFT JOIN courses co ON c.course_id = co.id
            SET v.level = COALESCE(co.level, 'N5'),
                v.category = COALESCE(c.title, 'Umum / Sehari-hari')
            WHERE v.chapter_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vocabularies', function (Blueprint $table) {
            $table->dropIndex(['level']);
            $table->dropIndex(['category']);
            $table->dropColumn(['level', 'category']);
            $table->unsignedBigInteger('chapter_id')->nullable(false)->change();
        });
    }
};
