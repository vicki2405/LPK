<?php

namespace Database\Seeders;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Database\Seeder;

class LmsCurriculumTenChaptersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sensei = User::role('sensei')->first() ?? User::first();

        // 1. Pastikan Kursus Utama Tersedia
        $course = Course::first();
        if (!$course) {
            $course = Course::create([
                'id' => 1,
                'created_by' => $sensei?->id,
                'title' => 'Bahasa Jepang Standar LPK (Minna no Nihongo N5 - N4)',
                'slug' => 'n4-minna-no-nihongo-standar',
                'level' => 'N4',
                'description' => 'Kurikulum terpadu bahasa Jepang untuk persiapan kerja Tokutei Ginou dan Magang (Moji-Goi, Bunpou, Dokkai, Choukai).',
                'is_published' => true,
                'order_index' => 1,
            ]);
        } else {
            $course->update([
                'title' => 'Bahasa Jepang Standar LPK (Minna no Nihongo N5 - N4)',
                'is_published' => true,
            ]);
        }

        $chaptersData = [
            [
                'number' => 1,
                'title' => '第1課 (Bab 1): Perkenalan Diri & Pola Kalimat Dasar (~ wa ~ desu)',
                'description' => "Pola Dasar:\n1. N1 は N2 です (N1 adalah N2)\n2. N1 は N2 じゃありません / ではありません (N1 bukan N2)\n3. N1 は N2 ですか (Apakah N1 adalah N2?)\n4. N も (Juga)\n5. N1 の N2 (Kepemilikan / Asal)",
                'lesson' => [
                    'title' => 'Tata Bahasa Bab 1: Partikel は (wa), も (mo), dan の (no)',
                    'content_type' => 'text_grammar',
                    'content_body' => "Pada bab pertama ini, kita mempelajari struktur dasar kalimat bahasa Jepang.\n\n■ 1. Kata Benda 1 + は + Kata Benda 2 + です\nPartikel は (dibaca 'wa') berfungsi sebagai penanda topik atau subjek kalimat.\nContoh: わたしは 田中(たなか)です (Saya adalah Tanaka).\n\n■ 2. Bentuk Negatif: ~じゃありません / ではありません\nContoh: サントスさんは 学生(がくせい)じゃありません (Sdr. Santos bukan mahasiswa).\n\n■ 3. Kalimat Tanya: ~ですか\nPartikel か di akhir kalimat berfungsi menggantikan tanda tanya (?).\nContoh: あなたは 実習生(じっしゅうせい)ですか (Apakah Anda peserta magang?).",
                    'duration_minutes' => 45,
                ],
                'vocabularies' => [
                    ['kanji' => '私', 'hiragana' => 'わたし', 'romaji' => 'watashi', 'meaning_id' => 'Saya', 'word_type' => 'Kata Ganti', 'example_sentence_jp' => 'わたしは インドネシア人です。', 'example_sentence_id' => 'Saya adalah orang Indonesia.'],
                    ['kanji' => '学生', 'hiragana' => 'がくせい', 'romaji' => 'gakusei', 'meaning_id' => 'Mahasiswa / Murid', 'word_type' => 'Kata Benda', 'example_sentence_jp' => 'アリさんは 学生です。', 'example_sentence_id' => 'Sdr. Ali adalah mahasiswa.'],
                    ['kanji' => '先生', 'hiragana' => 'せんせい', 'romaji' => 'sensei', 'meaning_id' => 'Guru / Pengajar', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '山田先生は 優しいです。', 'example_sentence_id' => 'Guru Yamada sangat ramah.'],
                    ['kanji' => '会社員', 'hiragana' => 'かいしゃいん', 'romaji' => 'kaishain', 'meaning_id' => 'Karyawan Perusahaan', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '父は 会社員です。', 'example_sentence_id' => 'Ayah saya adalah karyawan perusahaan.'],
                    ['kanji' => '病院', 'hiragana' => 'びょういん', 'romaji' => 'byouin', 'meaning_id' => 'Rumah Sakit', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '母は 病院で 働いています。', 'example_sentence_id' => 'Ibu bekerja di rumah sakit.'],
                    ['kanji' => '初めまして', 'hiragana' => 'はじめまして', 'romaji' => 'hajimemashite', 'meaning_id' => 'Senang berkenalan dengan Anda', 'word_type' => 'Ungkapan', 'example_sentence_jp' => '初めまして、どうぞ よろしく お願いします。', 'example_sentence_id' => 'Senang berkenalan, mohon bimbingannya.'],
                ]
            ],
            [
                'number' => 2,
                'title' => '第2課 (Bab 2): Kata Tunjuk Benda (これ, それ, あれ, この, その, あの)',
                'description' => "Pola Dasar:\n1. これ / それ / あれ は N です\n2. この / その / あの N は ... です\n3. そうです / そうじゃありません\n4. ~ か ~ か (Pilihan)",
                'lesson' => [
                    'title' => 'Tata Bahasa Bab 2: Kata Tunjuk Kore, Sore, Are',
                    'content_type' => 'text_grammar',
                    'content_body' => "Kata tunjuk benda dibagi berdasarkan jarak pembicara dan lawan bicara:\n- これ (Kore) = Ini (dekat pembicara)\n- それ (Sore) = Itu (dekat lawan bicara)\n- あれ (Are) = Itu (jauh dari keduanya)\n- どれ (Dore) = Yang mana?\n\nPerbedaan これ dan この:\n- これ langsung diikuti partikel: これは 本(ほん)です。\n- この harus menempel pada kata benda: この本は わたしのです。",
                    'duration_minutes' => 45,
                ],
                'vocabularies' => [
                    ['kanji' => '本', 'hiragana' => 'ほん', 'romaji' => 'hon', 'meaning_id' => 'Buku', 'word_type' => 'Kata Benda', 'example_sentence_jp' => 'これは 日本語の本です。', 'example_sentence_id' => 'Ini adalah buku bahasa Jepang.'],
                    ['kanji' => '辞書', 'hiragana' => 'じしょ', 'romaji' => 'jisho', 'meaning_id' => 'Kamus', 'word_type' => 'Kata Benda', 'example_sentence_jp' => 'それは だれの 辞書ですか。', 'example_sentence_id' => 'Itu kamus milik siapa?'],
                    ['kanji' => '雑誌', 'hiragana' => 'ざっし', 'romaji' => 'zasshi', 'meaning_id' => 'Majalah', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '自動車の 雑誌を 買いました。', 'example_sentence_id' => 'Saya membeli majalah mobil.'],
                    ['kanji' => '鍵', 'hiragana' => 'かぎ', 'romaji' => 'kagi', 'meaning_id' => 'Kunci', 'word_type' => 'Kata Benda', 'example_sentence_jp' => 'これは 部屋の 鍵です。', 'example_sentence_id' => 'Ini adalah kunci kamar.'],
                    ['kanji' => '時計', 'hiragana' => 'とけい', 'romaji' => 'tokei', 'meaning_id' => 'Jam / Arloji', 'word_type' => 'Kata Benda', 'example_sentence_jp' => 'あの時計は 高いです。', 'example_sentence_id' => 'Jam di sana itu mahal.'],
                    ['kanji' => '傘', 'hiragana' => 'かさ', 'romaji' => 'kasa', 'meaning_id' => 'Payung', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '雨ですから、傘を 持ちます。', 'example_sentence_id' => 'Karena hujan, saya membawa payung.'],
                ]
            ],
            [
                'number' => 3,
                'title' => '第3課 (Bab 3): Kata Tunjuk Tempat (ここ, そこ, あそこ, どこ)',
                'description' => "Pola Dasar:\n1. ここ / そこ / あそこ は N (tempat) です\n2. N は N (tempat) です\n3. どこ / どちら (Tanya Tempat / Arah Sopan)\n4. N1 の N2 (Asal Negara / Perusahaan Produk)",
                'lesson' => [
                    'title' => 'Tata Bahasa Bab 3: Menanyakan Lokasi dan Fasilitas',
                    'content_type' => 'text_grammar',
                    'content_body' => "■ Menunjukkan Tempat:\n- ここ (Koko) = Di sini\n- そこ (Soko) = Di situ\n- あそこ (Asoko) = Di sana\n- どこ (Doko) = Di mana?\n\n■ Bentuk Sopan Arah / Tempat:\n- こちら (Kochira), そちら (Sochira), あちら (Achira), どちら (Dochira)\n\nContoh: お手洗い(おてあらい)は どこですか (Toilet ada di mana?).",
                    'duration_minutes' => 45,
                ],
                'vocabularies' => [
                    ['kanji' => '教室', 'hiragana' => 'きょうしつ', 'romaji' => 'kyoushitsu', 'meaning_id' => 'Ruang Kelas', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '教室は 二階に あります。', 'example_sentence_id' => 'Ruang kelas ada di lantai dua.'],
                    ['kanji' => '事務所', 'hiragana' => 'じむしょ', 'romaji' => 'jimusho', 'meaning_id' => 'Kantor', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '先生は 事務所に います。', 'example_sentence_id' => 'Sensei ada di kantor.'],
                    ['kanji' => '食堂', 'hiragana' => 'しょくどう', 'romaji' => 'shokudou', 'meaning_id' => 'Kantin / Ruang Makan', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '食堂で 昼ごはんを 食べます。', 'example_sentence_id' => 'Makan siang di kantin.'],
                    ['kanji' => '部屋', 'hiragana' => 'へや', 'romaji' => 'heya', 'meaning_id' => 'Kamar / Ruangan', 'word_type' => 'Kata Benda', 'example_sentence_jp' => 'わたしの部屋は きれいです。', 'example_sentence_id' => 'Kamar saya bersih.'],
                    ['kanji' => '受付', 'hiragana' => 'うけつけ', 'romaji' => 'uketsuke', 'meaning_id' => 'Meja Resepsionis', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '受付で 名前を 書いてください。', 'example_sentence_id' => 'Silakan tulis nama di bagian resepsionis.'],
                    ['kanji' => '階', 'hiragana' => 'かい / がい', 'romaji' => 'kai', 'meaning_id' => 'Lantai (Tingkat)', 'word_type' => 'Satuan Hitung', 'example_sentence_jp' => 'エレベーターで ３階へ 行きます。', 'example_sentence_id' => 'Pergi ke lantai 3 menggunakan lift.'],
                ]
            ],
            [
                'number' => 4,
                'title' => '第4課 (Bab 4): Waktu, Jam, Menit, dan Kata Kerja Rutinitas (起きる, 寝る)',
                'description' => "Pola Dasar:\n1. 今 ~ 時 ~ 分 です (Sekarang jam ~ lewat ~ menit)\n2. V-ます / V-ません / V-ました / V-ませんでした\n3. N (waktu) に V (Pada jam ... melakukan ...)\n4. N1 から N2 まで (Dari ... sampai ...)\n5. N1 と N2 (Dan)",
                'lesson' => [
                    'title' => 'Tata Bahasa Bab 4: Jam, Menit, dan Kata Kerja Waktu Lampau',
                    'content_type' => 'text_grammar',
                    'content_body' => "■ Menyatakan Waktu:\n- Jam: [Angka] + 時 (じ / ji)\n- Menit: [Angka] + 分 (ふん / ぷん / fun/pun)\n- Setengah jam: 半 (はん / han)\n\n■ Konjugasi Kata Kerja Sopan (Bentuk Masu):\n- Positif Sekarang/Masa Depan: 起(お)きます (Bangun)\n- Negatif Sekarang/Masa Depan: 起きません (Tidak bangun)\n- Positif Lampau: 起きました (Tadi sudah bangun)\n- Negatif Lampau: 起きませんでした (Tadi tidak bangun)\n\nContoh: 毎朝(まいあさ) 6時(ろくじ)に 起きます (Setiap pagi bangun jam 6).",
                    'duration_minutes' => 50,
                ],
                'vocabularies' => [
                    ['kanji' => '起きます', 'hiragana' => 'おきます', 'romaji' => 'okimasu', 'meaning_id' => 'Bangun Tidur', 'word_type' => 'Kata Kerja II', 'example_sentence_jp' => '毎朝 ５時に 起きます。', 'example_sentence_id' => 'Setiap pagi bangun jam 5.'],
                    ['kanji' => '寝ます', 'hiragana' => 'ねます', 'romaji' => 'nemasu', 'meaning_id' => 'Tidur', 'word_type' => 'Kata Kerja II', 'example_sentence_jp' => '夜 １１時に 寝ます。', 'example_sentence_id' => 'Tidur jam 11 malam.'],
                    ['kanji' => '働きます', 'hiragana' => 'はたらきます', 'romaji' => 'hatarakimasu', 'meaning_id' => 'Bekerja', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => '工場で ８時間 働きます。', 'example_sentence_id' => 'Bekerja 8 jam di pabrik.'],
                    ['kanji' => '休みます', 'hiragana' => 'やすみます', 'romaji' => 'yasumimasu', 'meaning_id' => 'Istirahat / Libur', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => '日曜日は 会社を 休みます。', 'example_sentence_id' => 'Hari Minggu libur kerja.'],
                    ['kanji' => '勉強します', 'hiragana' => 'べんきょうします', 'romaji' => 'benkyoushimasu', 'meaning_id' => 'Belajar', 'word_type' => 'Kata Kerja III', 'example_sentence_jp' => '夜 日本語を 勉強します。', 'example_sentence_id' => 'Malam hari belajar bahasa Jepang.'],
                    ['kanji' => '銀行', 'hiragana' => 'ぎんこう', 'romaji' => 'ginkou', 'meaning_id' => 'Bank', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '銀行は ９時から ３時までです。', 'example_sentence_id' => 'Bank buka dari jam 9 sampai jam 3.'],
                ]
            ],
            [
                'number' => 5,
                'title' => '第5課 (Bab 5): Perpindahan & Transportasi (行く, 来る, 帰る)',
                'description' => "Pola Dasar:\n1. N (tempat) へ 行きます / 来ます / 帰ります\n2. どこ [へ] も 行きません (Tidak pergi ke mana pun)\n3. N (kendaraan) で 行きます (Pergi dengan kendaraan)\n4. N (orang) と 行きます (Pergi bersama orang)\n5. いつ (Kapan)",
                'lesson' => [
                    'title' => 'Tata Bahasa Bab 5: Partikel Perpindahan へ (e) dan Alat で (de)',
                    'content_type' => 'text_grammar',
                    'content_body' => "■ Partikel Perpindahan へ (dibaca 'e'):\nMenunjukkan arah tujuan perpindahan.\nContoh: 来週(らいしゅう) 日本(にほん)へ 行(い)きます (Minggu depan pergi ke Jepang).\n\n■ Partikel Sarana / Transportasi で (de):\nContoh: 新幹線(しんかんせん)で 東京(とうきょう)へ 行きます (Pergi ke Tokyo naik Shinkansen).\n*Catatan: Jalan kaki = 歩(ある)いて (tanpa partikel で).\n\n■ Partikel Teman Bersama と (to):\nContoh: 友達(ともだち)と 買い物(かいもの)へ 行きます (Pergi berbelanja bersama teman).",
                    'duration_minutes' => 50,
                ],
                'vocabularies' => [
                    ['kanji' => '行きます', 'hiragana' => 'いきます', 'romaji' => 'ikimasu', 'meaning_id' => 'Pergi', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => 'スーパーへ 行きます。', 'example_sentence_id' => 'Pergi ke supermarket.'],
                    ['kanji' => '来ます', 'hiragana' => 'きます', 'romaji' => 'kimasu', 'meaning_id' => 'Datang', 'word_type' => 'Kata Kerja III', 'example_sentence_jp' => '友達が うちへ 来ました。', 'example_sentence_id' => 'Teman datang ke rumah saya.'],
                    ['kanji' => '帰ります', 'hiragana' => 'かえります', 'romaji' => 'kaerimasu', 'meaning_id' => 'Pulang', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => '国へ 帰ります。', 'example_sentence_id' => 'Pulang ke negara asal.'],
                    ['kanji' => '飛行機', 'hiragana' => 'ひこうき', 'romaji' => 'hikouki', 'meaning_id' => 'Pesawat Terbang', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '飛行機で 日本へ 行きます。', 'example_sentence_id' => 'Pergi ke Jepang dengan pesawat terbang.'],
                    ['kanji' => '電車', 'hiragana' => 'でんしゃ', 'romaji' => 'densha', 'meaning_id' => 'Kereta Listrik', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '電車で 会社へ 通います。', 'example_sentence_id' => 'Berangkat ke kantor naik kereta listrik.'],
                    ['kanji' => '友達', 'hiragana' => 'ともだち', 'romaji' => 'tomodachi', 'meaning_id' => 'Teman / Sahabat', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '友達と 一緒に ご飯を 食べます。', 'example_sentence_id' => 'Makan bersama dengan teman.'],
                ]
            ],
            [
                'number' => 6,
                'title' => '第6課 (Bab 6): Aktivitas Objek & Tempat Tindakan (食べる, 飲む, ~を, ~で)',
                'description' => "Pola Dasar:\n1. N を V (transitif) (Melakukan aksi terhadap objek)\n2. 何 を しますか (Melakukan apa?)\n3. N (tempat) で V (Melakukan aksi di suatu tempat)\n4. V-ませんか (Maukah bersama-sama? / Mengajak)\n5. V-ましょう (Mari kita lakukan!)",
                'lesson' => [
                    'title' => 'Tata Bahasa Bab 6: Partikel Objek を (o) & Tempat Aksi で (de)',
                    'content_type' => 'text_grammar',
                    'content_body' => "■ Partikel Objek を (dibaca 'o'):\nMenghubungkan kata kerja dengan objek sasarannya.\nContoh: パンを 食(た)べます (Makan roti), 水(みず)を 飲(の)みます (Minum air).\n\n■ Partikel Tempat Aktivitas で (de):\nMenunjukkan tempat di mana sebuah aktivitas dilakukan.\nContoh: レストランで ご飯(はん)を 食べます (Makan di restoran).\n\n■ Pola Ajakan:\n- ~ませんか (Maukah Anda...? - lebih sopan)\n  Contoh: 一緒(いっしょ)に お茶(ちゃ)を 飲みませんか。\n- ~ましょう (Mari kita...! - langsung)\n  Contoh: ちょっと 休(やす)みましょう (Mari istirahat sebentar!).",
                    'duration_minutes' => 50,
                ],
                'vocabularies' => [
                    ['kanji' => '食べます', 'hiragana' => 'たべます', 'romaji' => 'tabemasu', 'meaning_id' => 'Makan', 'word_type' => 'Kata Kerja II', 'example_sentence_jp' => '朝ご飯を 食べましたか。', 'example_sentence_id' => 'Apakah Anda sudah makan pagi?'],
                    ['kanji' => '飲みます', 'hiragana' => 'のみます', 'romaji' => 'nomimasu', 'meaning_id' => 'Minum', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => '薬を 飲みます。', 'example_sentence_id' => 'Minum obat.'],
                    ['kanji' => '買います', 'hiragana' => 'かいます', 'romaji' => 'kaimasu', 'meaning_id' => 'Membeli', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => '新しい 靴を 買いました。', 'example_sentence_id' => 'Saya telah membeli sepatu baru.'],
                    ['kanji' => '見ます', 'hiragana' => 'みます', 'romaji' => 'mimasu', 'meaning_id' => 'Melihat / Menonton', 'word_type' => 'Kata Kerja II', 'example_sentence_jp' => 'テレビで ニュースを 見ます。', 'example_sentence_id' => 'Menonton berita di televisi.'],
                    ['kanji' => '聞きます', 'hiragana' => 'ききます', 'romaji' => 'kikimasu', 'meaning_id' => 'Mendengar / Menyimak', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => '音楽を 聞きます。', 'example_sentence_id' => 'Mendengarkan musik.'],
                    ['kanji' => '書きます', 'hiragana' => 'かきます', 'romaji' => 'kakimasu', 'meaning_id' => 'Menulis', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => '手紙を 書きました。', 'example_sentence_id' => 'Saya menulis surat.'],
                ]
            ],
            [
                'number' => 7,
                'title' => '第7課 (Bab 7): Alat, Bahasa, Memberi & Menerima (あげる, もらう, もう~ました)',
                'description' => "Pola Dasar:\n1. N (alat/sarana) で V (Melakukan dengan alat / bahasa tertentu)\n2. 'Kata/Kalimat' は ~語で 何ですか (Apa artinya dalam bahasa...?)\n3. N (orang) に あげます / もらいます (Memberi / Menerima)\n4. もう V-ました (Sudah selesai melakukan)\n5. まだです (Belum)",
                'lesson' => [
                    'title' => 'Tata Bahasa Bab 7: Alat Aksi & Hubungan Memberi-Menerima',
                    'content_type' => 'text_grammar',
                    'content_body' => "■ 1. Alat / Bahasa dengan で:\n- はしで 食べます (Makan menggunakan sumpit).\n- 日本語(にほんご)で レポートを 書きます (Menulis laporan dalam bahasa Jepang).\n\n■ 2. Memberi (あげる) dan Menerima (もらう):\n- [Pemberi] は [Penerima] に [Benda] を あげます。\n  Contoh: わたしは 田中さんに 花(はな)を あげました。\n- [Penerima] は [Pemberi] に/から [Benda] を もらいます。\n  Contoh: わたしは 先生に 本を もらいました。\n\n■ 3. Sudah (もう) vs Belum (まだ):\n- もう 昼(ひる)ご飯を 食べましたか。→ はい、もう 食べました / いいえ、まだです。",
                    'duration_minutes' => 50,
                ],
                'vocabularies' => [
                    ['kanji' => '切ります', 'hiragana' => 'きります', 'romaji' => 'kirimasu', 'meaning_id' => 'Memotong', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => 'ハサミで 紙を 切ります。', 'example_sentence_id' => 'Memotong kertas dengan gunting.'],
                    ['kanji' => '送ります', 'hiragana' => 'おくります', 'romaji' => 'okurimasu', 'meaning_id' => 'Mengirim', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => 'メールを 送りました。', 'example_sentence_id' => 'Saya sudah mengirim email.'],
                    ['kanji' => 'あげます', 'hiragana' => 'あげます', 'romaji' => 'agemasu', 'meaning_id' => 'Memberi (kepada orang lain)', 'word_type' => 'Kata Kerja II', 'example_sentence_jp' => '母に プレゼントを あげました。', 'example_sentence_id' => 'Memberikan kado kepada Ibu.'],
                    ['kanji' => 'もらいます', 'hiragana' => 'もらいます', 'romaji' => 'moraimasu', 'meaning_id' => 'Menerima / Mendapatkan', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => '先生から 辞書を もらいました。', 'example_sentence_id' => 'Menerima kamus dari Sensei.'],
                    ['kanji' => '貸します', 'hiragana' => 'かします', 'romaji' => 'kashimasu', 'meaning_id' => 'Meminjamkan', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => 'ペンを 貸してください。', 'example_sentence_id' => 'Tolong pinjamkan pulpen.'],
                    ['kanji' => '借ります', 'hiragana' => 'かります', 'romaji' => 'karimasu', 'meaning_id' => 'Meminjam', 'word_type' => 'Kata Kerja II', 'example_sentence_jp' => '図書館で 本を 借ります。', 'example_sentence_id' => 'Meminjam buku di perpustakaan.'],
                ]
            ],
            [
                'number' => 8,
                'title' => '第8課 (Bab 8): Kata Sifat I dan Kata Sifat Na (形容詞 Keiyoushi)',
                'description' => "Pola Dasar:\n1. N は い-Adj です / な-Adj です\n2. Negatif: ~くないです (I-Adj) / ~じゃありません (Na-Adj)\n3. N1 は どんな N2 ですか (N1 itu N2 yang bagaimana?)\n4. そして (Dan) vs が (Tetapi)",
                'lesson' => [
                    'title' => 'Tata Bahasa Bab 8: Perubahan Bentuk Kata Sifat I & Na',
                    'content_type' => 'text_grammar',
                    'content_body' => "Kata sifat dalam bahasa Jepang terbagi menjadi 2 kelompok besar:\n\n■ 1. Kata Sifat-I (Berakhiran huruf い):\n- Positif: 高(たか)いです (Mahal)\n- Negatif: 高くないです (Tidak mahal) → い diganti くない\n- Menerangkan Benda: 高い時計 (Jam yang mahal)\n\n■ 2. Kata Sifat-Na (Harus ditambah な saat menerangkan benda):\n- Positif: 親切(しんせつ)です (Ramah)\n- Negatif: 親切じゃありません (Tidak ramah)\n- Menerangkan Benda: 親切な人 (Orang yang ramah)\n\n■ Pengecualian Penting: いい (Bagus) → negatifnya: よくないです.",
                    'duration_minutes' => 50,
                ],
                'vocabularies' => [
                    ['kanji' => '大きい', 'hiragana' => 'おおきい', 'romaji' => 'ookii', 'meaning_id' => 'Besar', 'word_type' => 'Kata Sifat-I', 'example_sentence_jp' => 'この部屋は 大きいです。', 'example_sentence_id' => 'Kamar ini besar.'],
                    ['kanji' => '小さい', 'hiragana' => 'ちいさい', 'romaji' => 'chiisai', 'meaning_id' => 'Kecil', 'word_type' => 'Kata Sifat-I', 'example_sentence_jp' => '小さい 車を 運転します。', 'example_sentence_id' => 'Mengendarai mobil kecil.'],
                    ['kanji' => '新しい', 'hiragana' => 'あたらしい', 'romaji' => 'atarashii', 'meaning_id' => 'Baru', 'word_type' => 'Kata Sifat-I', 'example_sentence_jp' => '新しい 言葉を 覚えます。', 'example_sentence_id' => 'Menghafal kosakata baru.'],
                    ['kanji' => '親切', 'hiragana' => 'しんせつ', 'romaji' => 'shinsetsu', 'meaning_id' => 'Ramah / Baik Hati', 'word_type' => 'Kata Sifat-Na', 'example_sentence_jp' => '日本人は とても 親切です。', 'example_sentence_id' => 'Orang Jepang sangat ramah.'],
                    ['kanji' => '元気', 'hiragana' => 'げんき', 'romaji' => 'genki', 'meaning_id' => 'Sehat / Bersemangat', 'word_type' => 'Kata Sifat-Na', 'example_sentence_jp' => 'お元気ですか。はい、元気です。', 'example_sentence_id' => 'Apakah Anda sehat? Ya, saya sehat.'],
                    ['kanji' => '静か', 'hiragana' => 'しずか', 'romaji' => 'shizuka', 'meaning_id' => 'Tenang / Sunyi', 'word_type' => 'Kata Sifat-Na', 'example_sentence_jp' => '図書館は 静かです。', 'example_sentence_id' => 'Perpustakaan sangat tenang.'],
                ]
            ],
            [
                'number' => 9,
                'title' => '第9課 (Bab 9): Kesukaan, Kemahiran, Kepemilikan & Alasan (~が 好き, ~が わかる, ~から)',
                'description' => "Pola Dasar:\n1. N が 好きです / 嫌いです (Suka / Benci)\n2. N が 上手です / 下手です (Mahir / Kurang mahir)\n3. N が わかります / あります (Mengerti / Memiliki benda mati)\n4. Kata Keterangan: よく, だいたい, たくさん, すこし, ぜんぜん\n5. S1 から、S2 (Karena S1, maka S2)",
                'lesson' => [
                    'title' => 'Tata Bahasa Bab 9: Penggunaan Partikel が (ga) untuk Kemampuan & Kesukaan',
                    'content_type' => 'text_grammar',
                    'content_body' => "Kata kerja / sifat berikut menggunakan partikel が (bukan を):\n\n■ 1. Kesukaan & Keinginan:\n- 好き(すき) / 嫌(きら)い: 日本料理(にほんりょうり)が 好きです (Suka masakan Jepang).\n\n■ 2. Kemahiran:\n- 上手(じょうず) / 下手(へた): 歌(うた)が 上手です (Pandai menyanyi).\n\n■ 3. Kepahaman & Kepemilikan:\n- わかります: 日本語が わかります (Mengerti bahasa Jepang).\n- あります: お金(かね)が あります (Ada / punya uang).\n\n■ 4. Menyatakan Alasan (から - Kara):\nContoh: 時間(じかん)が ありませんから、タクシーで 行きます (Karena tidak ada waktu, saya pergi naik taksi).",
                    'duration_minutes' => 50,
                ],
                'vocabularies' => [
                    ['kanji' => '好き', 'hiragana' => 'すき', 'romaji' => 'suki', 'meaning_id' => 'Suka', 'word_type' => 'Kata Sifat-Na', 'example_sentence_jp' => 'わたしは 日本のアニメが 好きです。', 'example_sentence_id' => 'Saya suka anime Jepang.'],
                    ['kanji' => '上手', 'hiragana' => 'じょうず', 'romaji' => 'jouzu', 'meaning_id' => 'Mahir / Pandai', 'word_type' => 'Kata Sifat-Na', 'example_sentence_jp' => '田中さんは 料理が 上手です。', 'example_sentence_id' => 'Sdr. Tanaka pandai memasak.'],
                    ['kanji' => '下手', 'hiragana' => 'へた', 'romaji' => 'heta', 'meaning_id' => 'Kurang Mahir / Buruk', 'word_type' => 'Kata Sifat-Na', 'example_sentence_jp' => 'わたしは スポーツが 下手です。', 'example_sentence_id' => 'Saya kurang mahir berolahraga.'],
                    ['kanji' => '分かります', 'hiragana' => 'わかります', 'romaji' => 'wakarimasu', 'meaning_id' => 'Mengerti / Paham', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => '先生の 説明が よく 分かりました。', 'example_sentence_id' => 'Saya mengerti dengan baik penjelasan Sensei.'],
                    ['kanji' => 'あります', 'hiragana' => 'あります', 'romaji' => 'arimasu', 'meaning_id' => 'Ada / Memiliki (Benda Mati)', 'word_type' => 'Kata Kerja I', 'example_sentence_jp' => 'あした 試験が あります。', 'example_sentence_id' => 'Besok ada ujian.'],
                    ['kanji' => '時間', 'hiragana' => 'じかん', 'romaji' => 'jikan', 'meaning_id' => 'Waktu', 'word_type' => 'Kata Benda', 'example_sentence_jp' => '時間が ありませんから 急ぎましょう。', 'example_sentence_id' => 'Karena tidak ada waktu, mari kita bergegas.'],
                ]
            ],
            [
                'number' => 10,
                'title' => '第10課 (Bab 10): Keberadaan Makhluk Hidup & Benda (います, あります, Posisi Lokasi)',
                'description' => "Pola Dasar:\n1. N (tempat) に N が あります (Ada benda mati di tempat...)\n2. N (tempat) に N が います (Ada orang/hewan di tempat...)\n3. N は N (tempat) に あります / います\n4. Kosakata Posisi: 上 (atas), 下 (bawah), 前 (depan), 後ろ (belakang), 隣 (sebelah), 中 (dalam)\n5. N1 や N2 など (N1 dan N2, dan lain-lain)",
                'lesson' => [
                    'title' => 'Tata Bahasa Bab 10: Perbedaan あります vs います dan Posisi Ruang',
                    'content_type' => 'text_grammar',
                    'content_body' => "■ 1. あります (Untuk Benda Mati & Tanaman):\nContoh: 机(つくえ)の 上(うえ)に 本が あります (Di atas meja ada buku).\n\n■ 2. います (Untuk Manusia & Hewan Bergerak):\nContoh: 庭(にわ)に 犬(いぬ)が います (Di halaman ada anjing).\nContoh: 事務所(じむしょ)に 先生が います (Di kantor ada guru).\n\n■ 3. Posisi Spasial (Lokasi):\n- うえ (Atas), した (Bawah), まえ (Depan), うしろ (Belakang), となり (Sebelah), なか (Dalam), そと (Luar), ちかく (Dekat).\n\nContoh: 机と ベッドの 間(あいだ)に 猫(ねこ)が います (Di antara meja dan kasur ada kucing).",
                    'duration_minutes' => 50,
                ],
                'vocabularies' => [
                    ['kanji' => 'います', 'hiragana' => 'います', 'romaji' => 'imasu', 'meaning_id' => 'Ada (Manusia / Hewan)', 'word_type' => 'Kata Kerja II', 'example_sentence_jp' => '部屋に 誰か いますか。', 'example_sentence_id' => 'Apakah ada seseorang di dalam kamar?'],
                    ['kanji' => '上', 'hiragana' => 'うえ', 'romaji' => 'ue', 'meaning_id' => 'Atas', 'word_type' => 'Kata Posisi', 'example_sentence_jp' => '机の上に 鍵が あります。', 'example_sentence_id' => 'Di atas meja ada kunci.'],
                    ['kanji' => '下', 'hiragana' => 'した', 'romaji' => 'shita', 'meaning_id' => 'Bawah', 'word_type' => 'Kata Posisi', 'example_sentence_jp' => '椅子の下に 猫が います。', 'example_sentence_id' => 'Di bawah kursi ada kucing.'],
                    ['kanji' => '前', 'hiragana' => 'まえ', 'romaji' => 'mae', 'meaning_id' => 'Depan', 'word_type' => 'Kata Posisi', 'example_sentence_jp' => '駅の前に 交番が あります。', 'example_sentence_id' => 'Di depan stasiun ada pos polisi.'],
                    ['kanji' => '後ろ', 'hiragana' => 'うしろ', 'romaji' => 'ushiro', 'meaning_id' => 'Belakang', 'word_type' => 'Kata Posisi', 'example_sentence_jp' => '車の後ろに 子供が います。', 'example_sentence_id' => 'Di belakang mobil ada anak kecil.'],
                    ['kanji' => '隣', 'hiragana' => 'となり', 'romaji' => 'tonari', 'meaning_id' => 'Sebelah / Samping', 'word_type' => 'Kata Posisi', 'example_sentence_jp' => 'スーパーの隣に 銀行が あります。', 'example_sentence_id' => 'Di sebelah supermarket ada bank.'],
                ]
            ],
        ];

        foreach ($chaptersData as $chData) {
            $chapter = Chapter::updateOrCreate(
                [
                    'course_id' => $course->id,
                    'chapter_number' => $chData['number'],
                ],
                [
                    'title' => $chData['title'],
                    'description' => $chData['description'],
                    'order_index' => $chData['number'],
                    'is_published' => true,
                ]
            );

            // Lesson
            Lesson::updateOrCreate(
                [
                    'chapter_id' => $chapter->id,
                    'title' => $chData['lesson']['title'],
                ],
                [
                    'content_type' => $chData['lesson']['content_type'],
                    'content_body' => $chData['lesson']['content_body'],
                    'duration_minutes' => $chData['lesson']['duration_minutes'],
                    'order_index' => 1,
                    'is_published' => true,
                ]
            );

            // Vocabularies
            foreach ($chData['vocabularies'] as $vIdx => $vData) {
                Vocabulary::updateOrCreate(
                    [
                        'chapter_id' => $chapter->id,
                        'hiragana' => $vData['hiragana'],
                        'meaning_id' => $vData['meaning_id'],
                    ],
                    [
                        'kanji' => $vData['kanji'],
                        'romaji' => $vData['romaji'],
                        'word_type' => $vData['word_type'],
                        'example_sentence_jp' => $vData['example_sentence_jp'],
                        'example_sentence_id' => $vData['example_sentence_id'],
                        'order_index' => $vIdx + 1,
                    ]
                );
            }
        }
    }
}
