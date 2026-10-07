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
        // 1. Tabel Site Settings (Pengaturan Konten Website Dinamis)
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->longText('value')->nullable();
            $table->string('group')->default('general'); // general, hero, stats, about, contact, features
            $table->timestamps();
        });

        // 2. Tabel Testimoni Alumni di Jepang
        Schema::create('alumni_testimonials', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('photo')->nullable();
            $table->string('work_sector'); // contoh: "Caregiver (Kaigo)"
            $table->string('japan_location'); // contoh: "Tokyo, Jepang"
            $table->text('testimony_text');
            $table->string('program_type')->nullable(); // Tokutei Ginou / Magang
            $table->integer('order_index')->default(1);
            $table->boolean('is_published')->default(true);
            $table->timestamps();
        });

        // 3. Tambah field pendukung pada programs jika belum ada
        Schema::table('programs', function (Blueprint $table) {
            if (!Schema::hasColumn('programs', 'badge_label')) {
                $table->string('badge_label')->nullable()->after('description');
            }
            if (!Schema::hasColumn('programs', 'salary_range')) {
                $table->string('salary_range')->nullable()->after('badge_label');
            }
            if (!Schema::hasColumn('programs', 'benefits_json')) {
                $table->text('benefits_json')->nullable()->after('salary_range');
            }
            if (!Schema::hasColumn('programs', 'icon')) {
                $table->string('icon')->nullable()->after('benefits_json');
            }
        });

        // 4. Tambah field icon pada job_sectors jika belum ada
        Schema::table('job_sectors', function (Blueprint $table) {
            if (!Schema::hasColumn('job_sectors', 'icon')) {
                $table->string('icon')->nullable()->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_sectors', function (Blueprint $table) {
            if (Schema::hasColumn('job_sectors', 'icon')) {
                $table->dropColumn('icon');
            }
        });

        Schema::table('programs', function (Blueprint $table) {
            $table->dropColumn(['badge_label', 'salary_range', 'benefits_json', 'icon']);
        });

        Schema::dropIfExists('alumni_testimonials');
        Schema::dropIfExists('site_settings');
    }
};
