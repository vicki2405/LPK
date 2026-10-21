<?php

namespace Database\Seeders;

use App\Models\AlumniTestimonial;
use App\Models\JobSector;
use App\Models\Program;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class WebsiteSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. General & Identity Settings
        SiteSetting::set('lpk_name', 'LPK Masayume');
        SiteSetting::set('lpk_tagline', 'Wujudkan Karir Impian ke Jepang Bersama LPK Masayume');
        SiteSetting::set('lpk_legal_info', 'Izin Resmi Kemenaker RI & Terakreditasi SO');
        SiteSetting::set('lpk_topbar_subtitle', 'Program Tokutei Ginou (SSW) & Magang Kerja Jepang');
        SiteSetting::set('lpk_hero_badge', 'LEMBAGA PELATIHAN KERJA JEPANG RESMI & TERPERCAYA');
        SiteSetting::set('lpk_hero_description', 'Membuka jalan karir profesional di Jepang melalui Program Tokutei Ginou (SSW) dan Magang Kerja. Didukung kurikulum terpadu JLPT N4, pelatih Sensei berpengalaman, dan sistem ujian CBT Digital mutakhir.');

        // 2. Hero Registration Card Settings
        SiteSetting::set('reg_status_open', '1'); // 1 = Dibuka, 0 = Ditutup
        SiteSetting::set('reg_batch_title', 'Gelombang Angkatan Baru');
        SiteSetting::set('reg_card_subtitle', 'Target Kelulusan N4 & Penempatan Kerja');
        SiteSetting::set('reg_point_1_title', 'Target Kelulusan Terjamin');
        SiteSetting::set('reg_point_1_desc', 'Sertifikasi JLPT N4 & JFT-Basic A2 dalam waktu 4-6 bulan pembekalan.');
        SiteSetting::set('reg_point_2_title', 'Bebas Biaya Tak Terduga');
        SiteSetting::set('reg_point_2_desc', 'Transparan 100% rincian biaya pelatihan, paspor, MCU hingga tiket pesawat.');
        SiteSetting::set('reg_point_3_title', 'Penempatan Kerja Pasti');
        SiteSetting::set('reg_point_3_desc', 'Kerjasama resmi dengan puluhan Asosiasi Kumiai dan Perusahaan di seluruh Jepang.');

        // 3. Stats Promosi
        SiteSetting::set('stat_passing_rate', '98');
        SiteSetting::set('stat_passing_label', 'Tingkat Kelulusan Ujian N4');
        SiteSetting::set('stat_alumni_count', '350+');
        SiteSetting::set('stat_alumni_label', 'Siswa Berangkat ke Jepang');
        SiteSetting::set('stat_partner_count', '85+');
        SiteSetting::set('stat_partner_label', 'Mitra Perusahaan Jepang');
        SiteSetting::set('stat_legal_count', '100%');
        SiteSetting::set('stat_legal_label', 'Izin Resmi Kemenaker');

        // 4. Profil & Filosofi Lembaga
        SiteSetting::set('about_title', 'Mencetak Tenaga Kerja Profesional Berkarakter & Berkualitas untuk Industri Jepang');
        SiteSetting::set('about_p1', 'LPK Masayume (株式会社 正夢) adalah Lembaga Pelatihan Kerja terkemuka yang berfokus pada pembekalan bahasa, budaya, kedisiplinan (Hensachi & Aisatsu), serta keterampilan teknis kerja di Jepang.');
        SiteSetting::set('about_p2', 'Kami menggabungkan metode pembelajaran tradisional yang intensif dengan platform digital modern (LMS & CBT Standar Jepang) sehingga seluruh siswa dipantau progres kemampuan bahasanya secara presisi dari nol hingga siap berangkat.');
        SiteSetting::set('about_feature_1_title', 'Sensei Native & N2');
        SiteSetting::set('about_feature_1_desc', 'Pengajar berpengalaman lulusan universitas Jepang & bersertifikat resmi.');
        SiteSetting::set('about_feature_2_title', 'Asrama & Fasilitas');
        SiteSetting::set('about_feature_2_desc', 'Lingkungan belajar kondusif dengan laboratorium bahasa & lab CBT mandiri.');
        SiteSetting::set('philosophy_title', 'Mimpi Nyata Menuju Masa Depan');
        SiteSetting::set('philosophy_desc', 'Dalam bahasa Jepang, 正夢 (Masayume) berarti "Mimpi yang Menjadi Kenyataan". Kami berkomitmen mendampingi setiap siswa agar mimpi bekerja dan sukses di Jepang terwujud secara aman, legal, dan bermartabat.');

        // 5. Kontak & Lokasi
        SiteSetting::set('contact_phone', '0812-3456-7890');
        SiteSetting::set('contact_whatsapp', '6281234567890');
        SiteSetting::set('contact_email', 'info@masayume-lpk.co.id');
        SiteSetting::set('contact_address', 'Gedung LPK Masayume, Jl. Pelatihan Karir Jepang No. 88, Jawa Barat, Indonesia');
        SiteSetting::set('contact_hours', 'Senin - Sabtu: 08.00 - 17.00 WIB');
        SiteSetting::set('contact_maps_url', 'https://maps.google.com');

        // 6. Social Media
        SiteSetting::set('social_instagram', 'https://instagram.com');
        SiteSetting::set('social_facebook', 'https://facebook.com');
        SiteSetting::set('social_tiktok', 'https://tiktok.com');
        SiteSetting::set('social_youtube', 'https://youtube.com');

        // 7. Footer
        SiteSetting::set('footer_copyright', '© 2026 LPK Masayume (正夢). Hak Cipta Dilindungi Undang-Undang.');

        // Seed Sample Testimonials
        $testimonials = [
            [
                'name' => 'Rian Pratama',
                'work_sector' => 'Caregiver (Kaigo)',
                'japan_location' => 'Tokyo, Jepang',
                'program_type' => 'Tokutei Ginou',
                'testimony_text' => 'Alhamdulillah berkat bimbingan intensif Sensei di LPK Masayume, saya lulus N4 dalam 4 bulan dan langsung lolos wawancara user di Tokyo. Gaji dan fasilitas sesuai janji!',
                'order_index' => 1,
                'is_published' => true,
            ],
            [
                'name' => 'Siti Nurhaliza',
                'work_sector' => 'Pengolahan Makanan (Food Processing)',
                'japan_location' => 'Osaka, Jepang',
                'program_type' => 'Tokutei Ginou',
                'testimony_text' => 'LMS dan CBT Masayume sangat membantu latihan listening dan furigana. Begitu sampai di Osaka, tidak kaget dengan komunikasi bahasa Jepang sehari-hari.',
                'order_index' => 2,
                'is_published' => true,
            ],
            [
                'name' => 'Budi Santoso',
                'work_sector' => 'Manufaktur Komponen Mesin',
                'japan_location' => 'Aichi, Jepang',
                'program_type' => 'Magang 3 Tahun',
                'testimony_text' => 'Proses pengurusan COE dan Visa sangat transparan dan dibantu penuh sampai bandara Narita. Terima kasih LPK Masayume!',
                'order_index' => 3,
                'is_published' => true,
            ],
        ];

        foreach ($testimonials as $tData) {
            AlumniTestimonial::updateOrCreate(
                ['name' => $tData['name']],
                $tData
            );
        }

        // Seed default icons & details on programs
        Program::where('code', 'tokutei_ginou')->update([
            'icon' => '💼',
            'badge_label' => 'Program Unggulan',
            'salary_range' => '180.000 - 250.000 JPY/bln',
            'benefits_json' => [
                'Gaji Pokok 180.000 - 250.000 JPY/bln',
                'Syarat: JLPT N4 / JFT-Basic + Ujian Skill',
                'Kontrak Kerja Langsung dengan Perusahaan',
            ],
        ]);

        Program::where('code', 'magang')->update([
            'icon' => '🏭',
            'badge_label' => 'Magang Resmi 3-5 Tahun',
            'salary_range' => '130.000 - 170.000 JPY/bln',
            'benefits_json' => [
                'Uang Saku & Lembur Standar Jepang',
                'Syarat: Minimal Lulusan SMA/SMK Sederajat',
                'Fasilitas Asrama & Asuransi Lengkap',
            ],
        ]);

        // Seed default icons on job sectors
        $sectorIcons = [
            'KAIGO' => '🏥',
            'FOOD_BEV' => '🍱',
            'RESTAURANT' => '🍣',
            'AGRICULTURE' => '🌾',
            'MANUFACTURING' => '🏭',
            'CONSTRUCTION' => '🏗️',
            'AUTOMOTIVE' => '🚗',
            'HOTEL' => '🏨',
            'CLEANING' => '🧹',
            'LOGISTICS' => '📦',
        ];

        foreach ($sectorIcons as $code => $icon) {
            JobSector::where('code', $code)->update(['icon' => $icon]);
        }

        // Seed Sample Activity Galleries
        $galleries = [
            [
                'title' => 'Pelatihan Intensif Percakapan (Kaiwa) di Laboratorium Bahasa',
                'category' => 'pelatihan',
                'image_path' => 'https://images.unsplash.com/photo-1524178232363-1fb2b075b655?auto=format&fit=crop&w=800&q=80',
                'description' => 'Siswa angkatan terbaru sedang melatih percakapan kerja dan etika Aisatsu bersama Sensei.',
                'activity_date' => now()->subDays(12)->toDateString(),
                'order_index' => 1,
                'is_published' => true,
            ],
            [
                'title' => 'Simulasi Tryout Ujian CBT N4 & JFT-Basic A2 Mandiri',
                'category' => 'cbt',
                'image_path' => 'https://images.unsplash.com/photo-1531403009284-440f080d1e12?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pelaksanaan tryout CBT berkala dengan pengawasan ketat anti-cheat di laboratorium komputer.',
                'activity_date' => now()->subDays(20)->toDateString(),
                'order_index' => 2,
                'is_published' => true,
            ],
            [
                'title' => 'Wawancara Kerja (Mensetsu) User Jepang Sektor Kaigo',
                'category' => 'mensetsu',
                'image_path' => 'https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=800&q=80',
                'description' => 'Sesi wawancara daring langsung bersama pimpinan panti lansia (Social Welfare Corporation) dari Tokyo.',
                'activity_date' => now()->subDays(35)->toDateString(),
                'order_index' => 3,
                'is_published' => true,
            ],
            [
                'title' => 'Pelepasan Siswa Angkatan 14 Menuju Bandara Soekarno-Hatta ke Jepang',
                'category' => 'keberangkatan',
                'image_path' => 'https://images.unsplash.com/photo-1506015391300-4802dc74de2e?auto=format&fit=crop&w=800&q=80',
                'description' => 'Momen pelepasan haru dan pembekalan bandara sebelum terbang menuju Narita, Jepang.',
                'activity_date' => now()->subMonths(2)->toDateString(),
                'order_index' => 4,
                'is_published' => true,
            ],
            [
                'title' => 'Latihan Fisik, Kedisiplinan Pagi & Senam Radio Taiso Asrama',
                'category' => 'asrama',
                'image_path' => 'https://images.unsplash.com/photo-1571902943202-507ec2618e8f?auto=format&fit=crop&w=800&q=80',
                'description' => 'Pembiasaan senam Taiso dan kedisiplinan 5S setiap pagi untuk membentuk fisik prima siap kerja.',
                'activity_date' => now()->subDays(5)->toDateString(),
                'order_index' => 5,
                'is_published' => true,
            ],
            [
                'title' => 'Kunjungan Delegasi Asosiasi Kumiai dari Prefektur Aichi',
                'category' => 'mensetsu',
                'image_path' => 'https://images.unsplash.com/photo-1557804506-669a67965ba0?auto=format&fit=crop&w=800&q=80',
                'description' => 'Peninjauan langsung fasilitas pelatihan LPK Masayume oleh mitra Asosiasi Penerima dari Jepang.',
                'activity_date' => now()->subMonths(1)->toDateString(),
                'order_index' => 6,
                'is_published' => true,
            ],
        ];

        foreach ($galleries as $gData) {
            \App\Models\ActivityGallery::updateOrCreate(
                ['title' => $gData['title']],
                $gData
            );
        }
    }
}
