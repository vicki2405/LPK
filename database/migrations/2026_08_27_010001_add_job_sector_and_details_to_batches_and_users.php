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
        Schema::table('batches', function (Blueprint $table) {
            $table->string('job_sector')->nullable()->after('program_type'); // Contoh: "Pengolahan Makanan", "Manufaktur", "Pertanian", dll.
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('katakana_name')->nullable()->after('name'); // Contoh: "ブディ・サントソ"
            $table->string('nik', 20)->nullable()->after('email');
            $table->string('phone', 20)->nullable()->after('nik');
            $table->enum('gender', ['L', 'P'])->nullable()->after('phone');
            $table->string('birth_place')->nullable()->after('gender');
            $table->date('birth_date')->nullable()->after('birth_place');
            $table->string('target_job_sector')->nullable()->after('birth_date'); // Sektor kerja tujuan
            $table->string('passport_number')->nullable()->after('target_job_sector');
            $table->enum('mcu_status', ['pending', 'fit', 'unfit'])->default('pending')->after('passport_number');
            $table->enum('coe_status', ['pending', 'submitted', 'issued', 'rejected'])->default('pending')->after('mcu_status');
            $table->enum('visa_status', ['pending', 'issued'])->default('pending')->after('coe_status');
            $table->enum('pipeline_stage', ['pelatihan', 'lulus_n4', 'matching', 'mcu', 'coe', 'visa', 'terbang'])->default('pelatihan')->after('visa_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropColumn('job_sector');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'katakana_name', 'nik', 'phone', 'gender', 'birth_place', 
                'birth_date', 'target_job_sector', 'passport_number', 
                'mcu_status', 'coe_status', 'visa_status', 'pipeline_stage'
            ]);
        });
    }
};
