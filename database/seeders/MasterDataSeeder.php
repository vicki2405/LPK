<?php

namespace Database\Seeders;

use App\Models\JobSector;
use App\Models\PipelineStage;
use App\Models\Program;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Master Program Penyaluran LPK
        $programs = [
            ['name' => 'Tokutei Ginou (SSW)', 'name_jp' => '特定技能', 'code' => 'tokutei_ginou', 'sort_order' => 1],
            ['name' => 'Magang Kerja (Ginou Jisshusei)', 'name_jp' => '技能実習生', 'code' => 'magang', 'sort_order' => 2],
            ['name' => 'Engineering & IT (Gijinkoku)', 'name_jp' => '技術・人文知識・国際業務', 'code' => 'gijinkoku', 'sort_order' => 3],
            ['name' => 'Kursus Reguler Bahasa Jepang', 'name_jp' => '一般日本語コース', 'code' => 'reguler', 'sort_order' => 4],
        ];
        foreach ($programs as $prog) {
            Program::firstOrCreate(['code' => $prog['code']], $prog);
        }

        // 3. Master Sektor / Bidang Kerja
        $sectors = [
            ['name' => 'Pengolahan Makanan & Minuman', 'name_jp' => '飲食料品製造業', 'code' => 'FOOD_BEV', 'sort_order' => 1],
            ['name' => 'Manufaktur & Mesin Industri', 'name_jp' => '素形材・産業機械製造業', 'code' => 'MANUFACTURING', 'sort_order' => 2],
            ['name' => 'Pertanian & Peternakan', 'name_jp' => '農業・畜産業', 'code' => 'AGRICULTURE', 'sort_order' => 3],
            ['name' => 'Restoran & Food Service', 'name_jp' => '外食業', 'code' => 'RESTAURANT', 'sort_order' => 4],
            ['name' => 'Konstruksi & Bangunan', 'name_jp' => '建設業', 'code' => 'CONSTRUCTION', 'sort_order' => 5],
            ['name' => 'Kaigo / Perawat Lansia', 'name_jp' => '介護業', 'code' => 'CAREGIVER', 'sort_order' => 6],
            ['name' => 'Perhotelan & Akomodasi', 'name_jp' => '宿泊業', 'code' => 'HOTEL', 'sort_order' => 7],
            ['name' => 'Otomotif & Servis Mobil', 'name_jp' => '自動車整備業', 'code' => 'AUTOMOTIVE', 'sort_order' => 8],
            ['name' => 'Pembersihan Gedung', 'name_jp' => 'ビルクリーニング', 'code' => 'CLEANING', 'sort_order' => 9],
            ['name' => 'Perikanan & Budidaya Laut', 'name_jp' => '漁業・養殖業', 'code' => 'FISHERY', 'sort_order' => 10],
            ['name' => 'Engineering & IT', 'name_jp' => 'IT・エンジニアリング', 'code' => 'ENGINEERING_IT', 'sort_order' => 11],
        ];
        foreach ($sectors as $sector) {
            JobSector::firstOrCreate(['code' => $sector['code']], $sector);
        }

        // 4. Master Tahapan Penyaluran
        $stages = [
            ['name' => 'Pelatihan Bahasa & Budaya', 'name_jp' => '語学・マナー研修', 'code' => 'pelatihan', 'order_step' => 1, 'badge_color' => 'slate', 'icon' => 'BookOpen'],
            ['name' => 'Lulus JLPT N4 / JFT A2', 'name_jp' => 'N4/JFT合格', 'code' => 'lulus_n4', 'order_step' => 2, 'badge_color' => 'blue', 'icon' => 'Award'],
            ['name' => 'Matching / Interview Kumiai', 'name_jp' => '企業面接・内定', 'code' => 'matching', 'order_step' => 3, 'badge_color' => 'amber', 'icon' => 'Users'],
            ['name' => 'Pemeriksaan Medis (MCU Fit)', 'name_jp' => '健康診断 (MCU)', 'code' => 'mcu', 'order_step' => 4, 'badge_color' => 'purple', 'icon' => 'Activity'],
            ['name' => 'Pengajuan & Terbit COE Imigrasi', 'name_jp' => '在留資格(COE)取得', 'code' => 'coe', 'order_step' => 5, 'badge_color' => 'indigo', 'icon' => 'FileText'],
            ['name' => 'Pengajuan & Terbit Visa Kerja', 'name_jp' => '就労ビザ発給', 'code' => 'visa', 'order_step' => 6, 'badge_color' => 'emerald', 'icon' => 'ShieldCheck'],
            ['name' => 'Terbang & Bekerja di Jepang', 'name_jp' => '日本出国・就労開始', 'code' => 'terbang', 'order_step' => 7, 'badge_color' => 'rose', 'icon' => 'PlaneTakeoff'],
        ];
        foreach ($stages as $stage) {
            PipelineStage::firstOrCreate(['code' => $stage['code']], $stage);
        }
    }
}
