<?php

namespace Database\Seeders;

use App\Models\HeroSlide;
use Illuminate\Database\Seeder;

class HeroSlideSeeder extends Seeder
{
    public function run(): void
    {
        HeroSlide::truncate();

        HeroSlide::create([
            'title' => 'Gelombang Angkatan Baru - Tokutei Ginou Kaigo',
            'title_jp' => '新期生募集 - 特定技能 介護職プログラム',
            'badge_top_label' => 'TINGKAT KELULUSAN',
            'badge_top' => '98% JLPT N4 / JFT',
            'status_label' => 'Status: Dibuka',
            'status_label_jp' => '募集状況: 受付中',
            'image_path' => '/images/hero-japan.jpg',
            'salary_jpy' => '180k - 250k JPY',
            'salary_idr' => '± Rp 20 - 28 Juta/bln',
            'placement_location' => 'Tokyo, Osaka, Kanagawa',
            'placement_location_jp' => '東京・大阪・神奈川',
            'facilities' => 'Asrama & BPJS Jepang',
            'facilities_jp' => '社員寮・社会保険完備',
            'cta_text' => 'Daftar Angkatan Baru Sekarang →',
            'cta_text_jp' => '新期生募集に申し込む →',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        HeroSlide::create([
            'title' => 'Program Manufaktur & Mesin Industri Otomotif',
            'title_jp' => '特定技能 素形材・産業機械・自動車製造',
            'badge_top_label' => 'GAJI & OVERTIME TINGGI',
            'badge_top' => '200k - 270k JPY',
            'status_label' => 'Status: Dibuka',
            'status_label_jp' => '募集状況: 受付中',
            'image_path' => '/images/hero-manufacturing.jpg',
            'salary_jpy' => '200k - 270k JPY',
            'salary_idr' => '± Rp 22 - 30 Juta/bln',
            'placement_location' => 'Aichi, Shizuoka, Mie',
            'placement_location_jp' => '愛知・静岡・三重',
            'facilities' => 'Mess Pabrik & Uang Makan',
            'facilities_jp' => '工場社員寮・食事手当支給',
            'cta_text' => 'Konsultasi Manufaktur →',
            'cta_text_jp' => '製造分野について相談 →',
            'sort_order' => 2,
            'is_active' => true,
        ]);

        HeroSlide::create([
            'title' => 'Program Perhotelan & Pengolahan Makanan',
            'title_jp' => '宿泊業・外食業・飲食料品製造プログラム',
            'badge_top_label' => 'BEBAS BIAYA ASRAMA',
            'badge_top' => 'Benefit Lengkap',
            'status_label' => 'Status: Dibuka',
            'status_label_jp' => '募集状況: 受付中',
            'image_path' => '/images/hero-hospitality.jpg',
            'salary_jpy' => '185k - 245k JPY',
            'salary_idr' => '± Rp 20 - 27 Juta/bln',
            'placement_location' => 'Kyoto, Hokkaido, Fukuoka',
            'placement_location_jp' => '京都・北海道・福岡',
            'facilities' => 'Tempat Tinggal & Seragam',
            'facilities_jp' => '社宅完備・制服支給',
            'cta_text' => 'Daftar Perhotelan & Resto →',
            'cta_text_jp' => '外食・宿泊分野に申し込む →',
            'sort_order' => 3,
            'is_active' => true,
        ]);
    }
}
