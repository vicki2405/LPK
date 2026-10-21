<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Question;
use App\Models\QuestionCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LearningIndicatorsRealSeeder extends Seeder
{
    /**
     * Seed official, authentic LPK Learning Outcome Indicators (Indikator Capaian Pembelajaran Asli LPK).
     */
    public function run(): void
    {
        // 1. Ambil Bab 1-10 jika ada untuk penautan opsional
        $ch1 = Chapter::where('chapter_number', 1)->first();
        $ch2 = Chapter::where('chapter_number', 2)->first();
        $ch3 = Chapter::where('chapter_number', 3)->first();
        $ch4 = Chapter::where('chapter_number', 4)->first();
        $ch5 = Chapter::where('chapter_number', 5)->first();
        $ch6 = Chapter::where('chapter_number', 6)->first();
        $ch7 = Chapter::where('chapter_number', 7)->first();

        // 2. Definisi 14 Indikator Capaian Pembelajaran Asli Kurikulum LPK & Standar JLPT N5-N4
        $realIndicators = [
            // --- TATA BAHASA (BUNPOU) ---
            [
                'code' => 'IND-BUN-01',
                'name' => 'Konstruksi Kalimat Nominal & Partikel Topik (は・も・の)',
                'level' => 'N5',
                'level_code' => 'N5',
                'section_type' => 'bunpou',
                'chapter_id' => $ch1?->id,
                'description' => 'Mampu menyusun kalimat nominal dasar positif, negatif (~dewa arimasen), dan tanya (~desu ka) untuk menyatakan identitas diri, profesi, dan asal negara di lingkungan kerja.',
            ],
            [
                'code' => 'IND-BUN-02',
                'name' => 'Penunjuk Objek & Lokasi Ruang (これ・それ・あれ・ここ・そこ)',
                'level' => 'N5',
                'level_code' => 'N5',
                'section_type' => 'bunpou',
                'chapter_id' => $ch2?->id,
                'description' => 'Mampu menanyakan dan menunjukkan letak barang, peralatan kerja, dan posisi fasilitas kerja kepada supervisor secara presisi.',
            ],
            [
                'code' => 'IND-BUN-03',
                'name' => 'Waktu Kerja & Perpindahan (に・へ・から・まで・いきます)',
                'level' => 'N5',
                'level_code' => 'N5',
                'section_type' => 'bunpou',
                'chapter_id' => $ch4?->id,
                'description' => 'Mampu mengomunikasikan jadwal kerja harian, jam lembur, tanggal penugasan, serta moda transportasi perjalanan dinas.',
            ],
            [
                'code' => 'IND-BUN-04',
                'name' => 'Objek Tindakan Kerja & Alat Mesin (を・で・します)',
                'level' => 'N5',
                'level_code' => 'N5',
                'section_type' => 'bunpou',
                'chapter_id' => $ch6?->id,
                'description' => 'Mampu menyatakan tindakan operasional kerja, penggunaan alat perkakas/mesin, dan serah-terima barang.',
            ],
            [
                'code' => 'IND-BUN-05',
                'name' => 'Konjugasi Bentuk-Te & Instruksi Perintah (~てください・~てもいい)',
                'level' => 'N4',
                'level_code' => 'N4',
                'section_type' => 'bunpou',
                'chapter_id' => null, // Bebas / Lintas Bab
                'description' => 'Mampu memahami dan merespons instruksi kerja, permohonan izin kepada atasan, serta aturan yang diperbolehkan di asrama/tempat kerja.',
            ],
            [
                'code' => 'IND-BUN-06',
                'name' => 'Kepatuhan SOP & Larangan Kerja (~なければならない・~てはいけない)',
                'level' => 'N4',
                'level_code' => 'N4',
                'section_type' => 'bunpou',
                'chapter_id' => null,
                'description' => 'Mampu memahami rambu-rambu bahaya, kepatuhan keselamatan kerja, dan hal-hal yang dilarang keras (Kinshi) di tempat kerja Jepang.',
            ],

            // --- KOSAKATA & HURUF (MOJI-GOI) ---
            [
                'code' => 'IND-GOI-01',
                'name' => 'Kosakata Profesi, Relasi Kerja, & Perkenalan Diri',
                'level' => 'N5',
                'level_code' => 'N5',
                'section_type' => 'moji_goi',
                'chapter_id' => $ch1?->id,
                'description' => 'Menguasai kosakata jabatan, panggilan hierarki kerja (Kachou, Buchou, Senpai), dan salam resmi perkenalan kerja (Jikoshoukai).',
            ],
            [
                'code' => 'IND-GOI-02',
                'name' => 'Kosakata Perlengkapan Kerja & Fasilitas Pabrik/Kantor',
                'level' => 'N5',
                'level_code' => 'N5',
                'section_type' => 'moji_goi',
                'chapter_id' => $ch3?->id,
                'description' => 'Menguasai kosakata nama-nama perkakas, APD (Kouzou/Anzen-gutsu), bagian gedung pabrik, dan denah operasional kerja.',
            ],
            [
                'code' => 'IND-GOI-03',
                'name' => 'Kosakata Kata Kerja Operasional & Aktivitas Harian',
                'level' => 'N5',
                'level_code' => 'N5',
                'section_type' => 'moji_goi',
                'chapter_id' => $ch7?->id,
                'description' => 'Menguasai kata kerja aktivitas fisik kerja (tsukurimasu, hakobimasu, tsukaimasu, shuurishimasu, nomimasu, ikimasu).',
            ],
            [
                'code' => 'IND-GOI-04',
                'name' => 'Kosakata Budaya Kerja 5S & Keselamatan (Anzen Dai-ichi)',
                'level' => 'N4',
                'level_code' => 'N4',
                'section_type' => 'moji_goi',
                'chapter_id' => null,
                'description' => 'Memahami istilah Seiri (Ringkas), Seiton (Rapi), Seisou (Resik), Seiketsu (Rawat), dan Shitsuke (Rajin) serta pemilahan sampah.',
            ],

            // --- PEMAHAMAN BACAAN (DOKKAI) ---
            [
                'code' => 'IND-DOK-01',
                'name' => 'Membaca Pengumuman Jadwal Shift & Memo Kerja',
                'level' => 'N4',
                'level_code' => 'N4',
                'section_type' => 'dokkai',
                'chapter_id' => null,
                'description' => 'Mampu memahami informasi kalender kerja, jam kerja bergilir (koutai-kinmu), dan memo pada papan informasi (Keijiban).',
            ],
            [
                'code' => 'IND-DOK-02',
                'name' => 'Membaca Instruksi Formulir Permohonan & Catatan Sakit',
                'level' => 'N4',
                'level_code' => 'N4',
                'section_type' => 'dokkai',
                'chapter_id' => null,
                'description' => 'Mampu membaca formulir cuti (Yukyu), izin sakit (Byouki), dan formulir identitas diri standar perusahaan Jepang.',
            ],

            // --- MENYIMAK (CHOUKAI) ---
            [
                'code' => 'IND-CHOU-01',
                'name' => 'Menyimak Sapaan & Instruksi Spontan Mandor/Supervisor',
                'level' => 'N4',
                'level_code' => 'N4',
                'section_type' => 'choukai',
                'chapter_id' => null,
                'description' => 'Mampu menangkap maksud instruksi lisan cepat (chotto matte, kore katadukete) dan sapaan kerja harian.',
            ],
            [
                'code' => 'IND-CHOU-02',
                'name' => 'Menyimak Informasi Angka, Jam, & Kuantitas Pesanan',
                'level' => 'N5',
                'level_code' => 'N5',
                'section_type' => 'choukai',
                'chapter_id' => null,
                'description' => 'Mampu menyimak penyebutan jumlah barang, nomor kode pesanan, dan jam pertemuan lisan secara akurat.',
            ],
        ];

        $insertedMap = [];
        foreach ($realIndicators as $item) {
            $cat = QuestionCategory::updateOrCreate(
                ['code' => $item['code']],
                $item
            );
            $insertedMap[$item['code']] = $cat->id;
        }

        // 3. Tautkan Soal-Soal CBT yang Ada ke Indikator Asli yang Tepat Berdasarkan Konten Soal
        $allQuestions = Question::all();
        foreach ($allQuestions as $q) {
            $text = $q->question_text . ' ' . $q->instruction . ' ' . $q->explanation;
            
            // Logika Penataan Indikator Asli ke Butir Soal
            if ($q->section_type === 'choukai' || !empty($q->audio_url)) {
                if (str_contains($text, '時') || str_contains($text, '数') || str_contains($text, '番')) {
                    $q->question_category_id = $insertedMap['IND-CHOU-02'];
                } else {
                    $q->question_category_id = $insertedMap['IND-CHOU-01'];
                }
            } elseif ($q->section_type === 'dokkai' || !empty($q->reading_passage)) {
                if (str_contains($text, 'シフト') || str_contains($text, '予定') || str_contains($text, '掲示板')) {
                    $q->question_category_id = $insertedMap['IND-DOK-01'];
                } else {
                    $q->question_category_id = $insertedMap['IND-DOK-02'];
                }
            } elseif ($q->section_type === 'bunpou') {
                if (str_contains($text, 'てもいい') || str_contains($text, 'てください') || str_contains($text, 'てから')) {
                    $q->question_category_id = $insertedMap['IND-BUN-05'];
                } elseif (str_contains($text, 'なければ') || str_contains($text, 'いけません') || str_contains($text, '安全')) {
                    $q->question_category_id = $insertedMap['IND-BUN-06'];
                } elseif (str_contains($text, 'に') || str_contains($text, 'へ') || str_contains($text, 'から') || str_contains($text, 'まで')) {
                    $q->question_category_id = $insertedMap['IND-BUN-03'];
                } elseif (str_contains($text, 'を') || str_contains($text, 'で')) {
                    $q->question_category_id = $insertedMap['IND-BUN-04'];
                } elseif (str_contains($text, 'これ') || str_contains($text, 'それ') || str_contains($text, 'ここ')) {
                    $q->question_category_id = $insertedMap['IND-BUN-02'];
                } else {
                    $q->question_category_id = $insertedMap['IND-BUN-01'];
                }
            } else { // moji_goi
                if (str_contains($text, '研修') || str_contains($text, '実習生') || str_contains($text, '会社員')) {
                    $q->question_category_id = $insertedMap['IND-GOI-01'];
                } elseif (str_contains($text, '病院') || str_contains($text, '工場') || str_contains($text, '駅')) {
                    $q->question_category_id = $insertedMap['IND-GOI-02'];
                } elseif (str_contains($text, '5S') || str_contains($text, '分別') || str_contains($text, '安全') || str_contains($text, 'ゴミ')) {
                    $q->question_category_id = $insertedMap['IND-GOI-04'];
                } else {
                    $q->question_category_id = $insertedMap['IND-GOI-03'];
                }
            }
            $q->save();
        }

        // 4. Bersihkan Kategori Dummy Lama yang Tidak Memiliki Kode Resmi dan Tidak Digunakan
        $validIds = array_values($insertedMap);
        QuestionCategory::whereNotIn('id', $validIds)
            ->whereDoesntHave('questions')
            ->delete();
    }
}
