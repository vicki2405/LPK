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
            // 1. GROUP VOCABULARIES (KOTOBA) - 6 KATA PER TOPIK
            // ==========================================
            $vocabTopics = [
                [
                    'title' => 'Perkenalan Diri & Profesi',
                    'description' => 'Kosakata sapaan awal, identitas, dan pekerjaan (自己紹介と職業)',
                    'level' => 'N5',
                    'sort_order' => 1,
                    'words' => ['私', '学生', '先生', '会社員', '病院', '初めまして'],
                ],
                [
                    'title' => 'Benda Sehari-hari & Sekolah',
                    'description' => 'Benda-benda di sekitar kelas dan perlengkapan belajar (身の回りの物・学用品)',
                    'level' => 'N5',
                    'sort_order' => 2,
                    'words' => ['本', '辞書', '雑誌', '鍵', '時計', '傘'],
                ],
                [
                    'title' => 'Ruangan & Fasilitas Bangunan',
                    'description' => 'Nama ruangan, fasilitas sekolah/kantor dan lantai gedung (施設と部屋・建物)',
                    'level' => 'N5',
                    'sort_order' => 3,
                    'words' => ['教室', '事務所', '食堂', '部屋', '受付', '階'],
                ],
                [
                    'title' => 'Rutinitas & Jam Kerja',
                    'description' => 'Aktivitas bangun, tidur, belajar, bekerja, dan istirahat (毎日の日課と時間)',
                    'level' => 'N5',
                    'sort_order' => 4,
                    'words' => ['起きます', '寝ます', '働きます', '休みます', '勉強します', '銀行'],
                ],
                [
                    'title' => 'Perpindahan & Transportasi',
                    'description' => 'Kata kerja arah gerak dan sarana transportasi (移動動詞と乗り物)',
                    'level' => 'N5',
                    'sort_order' => 5,
                    'words' => ['行きます', '来ます', '帰ります', '飛行機', '電車', '友達'],
                ],
                [
                    'title' => 'Aktivitas Sehari-hari',
                    'description' => 'Kata kerja makan, minum, membeli, melihat, mendengar, menulis (日常生活の動詞)',
                    'level' => 'N5',
                    'sort_order' => 6,
                    'words' => ['食べます', '飲みます', '買います', '見ます', '聞きます', '書きます'],
                ],
                [
                    'title' => 'Transaksi & Pertukaran Benda',
                    'description' => 'Kata kerja memberi, menerima, meminjam, meminjamkan, memotong, mengirim (やりもらいと授受動詞)',
                    'level' => 'N5',
                    'sort_order' => 7,
                    'words' => ['切ります', '送ります', 'あげます', 'もらいます', '貸します', '借ります'],
                ],
                [
                    'title' => 'Sifat & Kondisi Lingkungan',
                    'description' => 'Kata sifat untuk mendeskripsikan benda, sifat orang, dan suasana (形容詞・様子と性格)',
                    'level' => 'N5',
                    'sort_order' => 8,
                    'words' => ['大きい', '小さい', '新しい', '親切', '元気', '静か'],
                ],
                [
                    'title' => 'Kemampuan, Minat & Waktu',
                    'description' => 'Menyatakan kesukaan, kepandaian, pemahaman, dan waktu (能力・好み・時間)',
                    'level' => 'N5',
                    'sort_order' => 9,
                    'words' => ['好き', '上手', '下手', '分かります', 'あります', '時間'],
                ],
                [
                    'title' => 'Keberadaan & Posisi Ruang',
                    'description' => 'Keberadaan makhluk hidup dan petunjuk letak / arah ruang (存在動詞と位置関係)',
                    'level' => 'N5',
                    'sort_order' => 10,
                    'words' => ['います', '上', '下', '前', '後ろ', '隣'],
                ],
            ];

            // Hapus topik kosakata di atas urutan 10 jika ada topik lama
            Topic::forVocabulary()->where('sort_order', '>', 10)->delete();

            foreach ($vocabTopics as $item) {
                $topic = Topic::updateOrCreate(
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

                // Ambil tepat 6 vocabularies yang sesuai kata kunci
                $vocabIds = Vocabulary::whereIn('kanji', $item['words'])
                    ->take(6)
                    ->pluck('id')
                    ->toArray();

                $topic->vocabularies()->sync($vocabIds);

                if (!empty($vocabIds)) {
                    Vocabulary::whereIn('id', $vocabIds)->update([
                        'category' => $item['title'],
                        'level' => $item['level'],
                    ]);
                }
            }

            // ==========================================
            // 2. GROUP KANJI - 6 HURUF KANJI PER TOPIK
            // ==========================================
            $kanjiTopics = [
                [
                    'title' => 'Hari & Kalender',
                    'description' => 'Kanji hari dalam sepekan dan elemen alam dasar (曜日と自然元素)',
                    'level' => 'N5',
                    'sort_order' => 1,
                    'kanjis' => ['日', '月', '火', '水', '木', '金'],
                ],
                [
                    'title' => 'Elemen & Bentang Alam',
                    'description' => 'Kanji bentuk alam gunung, sungai, dan cuaca (自然地形・山川・天気)',
                    'level' => 'N5',
                    'sort_order' => 2,
                    'kanjis' => ['土', '山', '川', '天', '気', '雨'],
                ],
                [
                    'title' => 'Sosok Manusia & Keluarga',
                    'description' => 'Kanji sosok manusia, anak, perempuan, dan keluarga (人と家族)',
                    'level' => 'N5',
                    'sort_order' => 3,
                    'kanjis' => ['人', '子', '女', '男', '父', '母'],
                ],
                [
                    'title' => 'Pendidikan & Lembaga',
                    'description' => 'Kanji belajar, sekolah, dan guru (学び・学校・先生)',
                    'level' => 'N5',
                    'sort_order' => 4,
                    'kanjis' => ['先', '生', '学', '校', '本', '友'],
                ],
                [
                    'title' => 'Aktivitas & Aksi Dasar',
                    'description' => 'Kanji kata kerja makan, minum, melihat, mendengar, pergi, datang (基本動作・生活)',
                    'level' => 'N5',
                    'sort_order' => 5,
                    'kanjis' => ['行', '来', '食', '飲', '見', '聞'],
                ],
                [
                    'title' => 'Ukuran & Posisi/Arah',
                    'description' => 'Kanji ukuran besar kecil dan penunjuk arah posisi (大小・位置・方向)',
                    'level' => 'N5',
                    'sort_order' => 6,
                    'kanjis' => ['大', '小', '上', '下', '中', '前'],
                ],
            ];

            // Hapus topik kanji lama yang tidak sesuai
            $validKanjiTitles = array_column($kanjiTopics, 'title');
            Topic::forKanji()->whereNotIn('title', $validKanjiTitles)->delete();

            foreach ($kanjiTopics as $item) {
                $topic = Topic::updateOrCreate(
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

                $kanjiIds = Kanji::whereIn('kanji', $item['kanjis'])
                    ->take(6)
                    ->pluck('id')
                    ->toArray();

                $topic->kanjis()->sync($kanjiIds);
            }
        });
    }
}
