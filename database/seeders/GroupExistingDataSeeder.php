<?php

namespace Database\Seeders;

use App\Models\Kanji;
use App\Models\Topic;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupExistingDataSeeder extends Seeder
{
    public function run(): void
    {
        $creator = User::role(['sensei', 'admin'])->first() ?? User::first();
        $creatorId = $creator ? $creator->id : 1;

        DB::transaction(function () use ($creatorId) {
            // ==========================================
            // 1. GROUP VOCABULARIES (KOTOBA)
            // ==========================================
            $vocabTopics = [
                [
                    'title' => 'Perkenalan Diri & Profesi',
                    'description' => 'Kosakata sapaan awal, identitas, dan pekerjaan (自己紹介と職業)',
                    'level' => 'N5',
                    'sort_order' => 1,
                    'vocab_ids' => [6, 7, 8, 9, 10, 11],
                ],
                [
                    'title' => 'Benda Sehari-hari & Sekolah',
                    'description' => 'Benda-benda di sekitar kelas dan perlengkapan belajar (身の回りの物・学用品)',
                    'level' => 'N5',
                    'sort_order' => 2,
                    'vocab_ids' => [12, 13, 14, 15, 16, 17],
                ],
                [
                    'title' => 'Ruangan & Fasilitas Bangunan',
                    'description' => 'Nama ruangan, fasilitas sekolah/kantor dan lantai gedung (施設と部屋・建物)',
                    'level' => 'N5',
                    'sort_order' => 3,
                    'vocab_ids' => [18, 19, 20, 21, 22, 23],
                ],
                [
                    'title' => 'Rutinitas & Jam Kerja',
                    'description' => 'Aktivitas bangun, tidur, belajar, bekerja, dan istirahat (毎日の日課と時間)',
                    'level' => 'N5',
                    'sort_order' => 4,
                    'vocab_ids' => [24, 25, 26, 27, 28, 29],
                ],
                [
                    'title' => 'Perpindahan & Transportasi',
                    'description' => 'Kata kerja arah gerak dan sarana transportasi (移動動詞と乗り物)',
                    'level' => 'N5',
                    'sort_order' => 5,
                    'vocab_ids' => [30, 31, 32, 33, 34, 35],
                ],
                [
                    'title' => 'Aktivitas Sehari-hari',
                    'description' => 'Kata kerja makan, minum, membeli, melihat, mendengar, menulis (日常生活の動詞)',
                    'level' => 'N5',
                    'sort_order' => 6,
                    'vocab_ids' => [36, 37, 38, 39, 40, 41],
                ],
                [
                    'title' => 'Transaksi & Pemberian Benda',
                    'description' => 'Kata kerja memberi, menerima, meminjam, meminjamkan, memotong, mengirim (やりもらいと授受動詞)',
                    'level' => 'N5',
                    'sort_order' => 7,
                    'vocab_ids' => [42, 43, 44, 45, 46, 47],
                ],
                [
                    'title' => 'Sifat & Kondisi Lingkungan',
                    'description' => 'Kata sifat untuk mendeskripsikan benda, sifat orang, dan suasana (形容詞・様子と性格)',
                    'level' => 'N5',
                    'sort_order' => 8,
                    'vocab_ids' => [48, 49, 50, 51, 52, 53],
                ],
                [
                    'title' => 'Kemampuan, Minat & Kepemilikan',
                    'description' => 'Menyatakan kesukaan, kepandaian, pemahaman, dan waktu (能力・好み・時間)',
                    'level' => 'N5',
                    'sort_order' => 9,
                    'vocab_ids' => [54, 55, 56, 57, 58, 59],
                ],
                [
                    'title' => 'Keberadaan & Posisi Ruang',
                    'description' => 'Keberadaan makhluk hidup dan petunjuk letak / arah ruang (存在動詞と位置関係)',
                    'level' => 'N5',
                    'sort_order' => 10,
                    'vocab_ids' => [60, 61, 62, 63, 64, 65],
                ],
                [
                    'title' => 'Tugas & Ketepatan Waktu',
                    'description' => 'Pencarian, ketepatan waktu, dan pengawasan kegiatan (作業・確認・時間厳守)',
                    'level' => 'N5',
                    'sort_order' => 11,
                    'vocab_ids' => [1, 2, 3, 4, 5],
                ],
            ];

            foreach ($vocabTopics as $item) {
                $topic = Topic::firstOrCreate(
                    [
                        'type' => 'vocabulary',
                        'level' => $item['level'],
                        'title' => $item['title'],
                    ],
                    [
                        'description' => $item['description'],
                        'sort_order' => $item['sort_order'],
                        'created_by' => $creatorId,
                    ]
                );

                $topic->vocabularies()->syncWithoutDetaching($item['vocab_ids']);

                Vocabulary::whereIn('id', $item['vocab_ids'])->update([
                    'category' => $item['title'],
                ]);
            }

            // ==========================================
            // 2. GROUP KANJI
            // ==========================================
            $kanjiTopics = [
                [
                    'title' => 'Hari & Elemen Alam',
                    'description' => 'Kanji hari dalam sepekan dan elemen alam dasar (曜日と自然元素)',
                    'level' => 'N5',
                    'sort_order' => 1,
                    'kanji_ids' => [1, 2, 3, 4, 5, 6, 7],
                ],
                [
                    'title' => 'Bentang Alam & Geografi',
                    'description' => 'Kanji bentuk alam gunung dan sungai (自然地形・山川)',
                    'level' => 'N5',
                    'sort_order' => 2,
                    'kanji_ids' => [8, 9],
                ],
                [
                    'title' => 'Manusia & Keluarga',
                    'description' => 'Kanji sosok manusia, anak, perempuan, dan laki-laki (人と家族)',
                    'level' => 'N5',
                    'sort_order' => 3,
                    'kanji_ids' => [10, 11, 12, 13],
                ],
                [
                    'title' => 'Pendidikan & Sekolah',
                    'description' => 'Kanji belajar, sekolah, dan guru (学び・学校・先生)',
                    'level' => 'N5',
                    'sort_order' => 4,
                    'kanji_ids' => [14, 15, 16, 17, 23],
                ],
                [
                    'title' => 'Aktivitas & Pergerakan',
                    'description' => 'Kanji kata kerja makan, minum, melihat, pergi, datang (基本動作・移動)',
                    'level' => 'N5',
                    'sort_order' => 5,
                    'kanji_ids' => [18, 19, 20, 21, 22],
                ],
                [
                    'title' => 'Ukuran & Dimensi',
                    'description' => 'Kanji ukuran besar dan kecil (大小・寸法)',
                    'level' => 'N5',
                    'sort_order' => 6,
                    'kanji_ids' => [24, 25],
                ],
            ];

            foreach ($kanjiTopics as $item) {
                $topic = Topic::firstOrCreate(
                    [
                        'type' => 'kanji',
                        'level' => $item['level'],
                        'title' => $item['title'],
                    ],
                    [
                        'description' => $item['description'],
                        'sort_order' => $item['sort_order'],
                        'created_by' => $creatorId,
                    ]
                );

                $topic->kanjis()->syncWithoutDetaching($item['kanji_ids']);
            }
        });
    }
}
