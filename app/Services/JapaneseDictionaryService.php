<?php

namespace App\Services;

use App\Models\Vocabulary;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class JapaneseDictionaryService
{
    /**
     * Dictionary database of frequent Indonesian -> Japanese words with Kanji, Hiragana, Romaji, and category/type.
     */
    protected static array $dictionary = [
        // ================= KATA BENDA (NOUNS) =================
        'guru' => [
            ['kanji' => '先生', 'hiragana' => 'せんせい', 'romaji' => 'sensei', 'word_type' => 'Kata Benda', 'meaning' => 'Guru / Pengajar / Panggilan Hormat'],
            ['kanji' => '教師', 'hiragana' => 'きょうし', 'romaji' => 'kyoushi', 'word_type' => 'Kata Benda', 'meaning' => 'Guru (Nama Profesi)'],
        ],
        'pengajar' => [
            ['kanji' => '先生', 'hiragana' => 'せんせい', 'romaji' => 'sensei', 'word_type' => 'Kata Benda', 'meaning' => 'Guru / Pengajar'],
            ['kanji' => '講師', 'hiragana' => 'こうし', 'romaji' => 'koushi', 'word_type' => 'Kata Benda', 'meaning' => 'Dosen / Instruktur'],
        ],
        'murid' => [
            ['kanji' => '学生', 'hiragana' => 'がくせい', 'romaji' => 'gakusei', 'word_type' => 'Kata Benda', 'meaning' => 'Siswa / Mahasiswa'],
            ['kanji' => '生徒', 'hiragana' => 'せいと', 'romaji' => 'seito', 'word_type' => 'Kata Benda', 'meaning' => 'Murid / Pelajar'],
        ],
        'siswa' => [
            ['kanji' => '学生', 'hiragana' => 'がくせい', 'romaji' => 'gakusei', 'word_type' => 'Kata Benda', 'meaning' => 'Siswa / Mahasiswa'],
            ['kanji' => '実習生', 'hiragana' => 'じっしゅうせい', 'romaji' => 'jisshuusei', 'word_type' => 'Kata Benda', 'meaning' => 'Pemagang / Trainee'],
        ],
        'sekolah' => [
            ['kanji' => '学校', 'hiragana' => 'がっこう', 'romaji' => 'gakkou', 'word_type' => 'Kata Benda', 'meaning' => 'Sekolah'],
        ],
        'universitas' => [
            ['kanji' => '大学', 'hiragana' => 'だいがく', 'romaji' => 'daigaku', 'word_type' => 'Kata Benda', 'meaning' => 'Universitas / Perguruan Tinggi'],
        ],
        'rumah sakit' => [
            ['kanji' => '病院', 'hiragana' => 'びょういん', 'romaji' => 'byouin', 'word_type' => 'Kata Benda', 'meaning' => 'Rumah Sakit'],
        ],
        'dokter' => [
            ['kanji' => '医者', 'hiragana' => 'いしゃ', 'romaji' => 'isha', 'word_type' => 'Kata Benda', 'meaning' => 'Dokter'],
        ],
        'perawat' => [
            ['kanji' => '看護師', 'hiragana' => 'かんごし', 'romaji' => 'kangoshi', 'word_type' => 'Kata Benda', 'meaning' => 'Perawat Medis'],
            ['kanji' => '介護士', 'hiragana' => 'かいごし', 'romaji' => 'kaigoshi', 'word_type' => 'Kata Benda', 'meaning' => 'Perawat Lansia / Careworker'],
        ],
        'kaigo' => [
            ['kanji' => '介護', 'hiragana' => 'かいご', 'romaji' => 'kaigo', 'word_type' => 'Kata Benda', 'meaning' => 'Perawatan Lansia'],
            ['kanji' => '介護士', 'hiragana' => 'かいごし', 'romaji' => 'kaigoshi', 'word_type' => 'Kata Benda', 'meaning' => 'Perawat Lansia'],
        ],
        'perusahaan' => [
            ['kanji' => '会社', 'hiragana' => 'かいしゃ', 'romaji' => 'kaisha', 'word_type' => 'Kata Benda', 'meaning' => 'Perusahaan / Kantor'],
        ],
        'kantor' => [
            ['kanji' => '会社', 'hiragana' => 'かいしゃ', 'romaji' => 'kaisha', 'word_type' => 'Kata Benda', 'meaning' => 'Perusahaan'],
            ['kanji' => '事務所', 'hiragana' => 'じむしょ', 'romaji' => 'jimusho', 'word_type' => 'Kata Benda', 'meaning' => 'Kantor / Ruang Kerja'],
        ],
        'pabrik' => [
            ['kanji' => '工場', 'hiragana' => 'こうじょう', 'romaji' => 'koujou', 'word_type' => 'Kata Benda', 'meaning' => 'Pabrik / Bengkel Kerja'],
        ],
        'karyawan' => [
            ['kanji' => '会社員', 'hiragana' => 'かいしゃいん', 'romaji' => 'kaishain', 'word_type' => 'Kata Benda', 'meaning' => 'Karyawan Perusahaan'],
            ['kanji' => '社員', 'hiragana' => 'しゃいん', 'romaji' => 'shain', 'word_type' => 'Kata Benda', 'meaning' => 'Karyawan / Staf'],
        ],
        'bank' => [
            ['kanji' => '銀行', 'hiragana' => 'ぎんこう', 'romaji' => 'ginkou', 'word_type' => 'Kata Benda', 'meaning' => 'Bank'],
        ],
        'stasiun' => [
            ['kanji' => '駅', 'hiragana' => 'えき', 'romaji' => 'eki', 'word_type' => 'Kata Benda', 'meaning' => 'Stasiun'],
        ],
        'bandara' => [
            ['kanji' => '空港', 'hiragana' => 'くうこう', 'romaji' => 'kuukou', 'word_type' => 'Kata Benda', 'meaning' => 'Bandara / Lapangan Terbang'],
        ],
        'restoran' => [
            ['kanji' => 'レストラン', 'hiragana' => 'れすとらん', 'romaji' => 'resutoran', 'word_type' => 'Kata Benda', 'meaning' => 'Restoran'],
            ['kanji' => '食堂', 'hiragana' => 'しょくどう', 'romaji' => 'shokudou', 'word_type' => 'Kata Benda', 'meaning' => 'Kantin / Rumah Makan'],
        ],
        'toko' => [
            ['kanji' => '店', 'hiragana' => 'みせ', 'romaji' => 'mise', 'word_type' => 'Kata Benda', 'meaning' => 'Toko'],
        ],
        'rumah' => [
            ['kanji' => '家', 'hiragana' => 'いえ', 'romaji' => 'ie', 'word_type' => 'Kata Benda', 'meaning' => 'Rumah'],
            ['kanji' => 'うち', 'hiragana' => 'うち', 'romaji' => 'uchi', 'word_type' => 'Kata Benda', 'meaning' => 'Rumah Saya'],
        ],
        'kamar' => [
            ['kanji' => '部屋', 'hiragana' => 'へや', 'romaji' => 'heya', 'word_type' => 'Kata Benda', 'meaning' => 'Kamar / Ruangan'],
        ],
        'toilet' => [
            ['kanji' => 'トイレ', 'hiragana' => 'といれ', 'romaji' => 'toire', 'word_type' => 'Kata Benda', 'meaning' => 'Toilet / Kamar Kecil'],
            ['kanji' => 'お手洗い', 'hiragana' => 'おてあらい', 'romaji' => 'otearai', 'word_type' => 'Kata Benda', 'meaning' => 'Toilet (Bentuk Sopan)'],
        ],
        'kamar mandi' => [
            ['kanji' => 'お風呂', 'hiragana' => 'おふろ', 'romaji' => 'ofuro', 'word_type' => 'Kata Benda', 'meaning' => 'Bak Mandi / Kamar Mandi'],
            ['kanji' => 'シャワー室', 'hiragana' => 'しゃわーしつ', 'romaji' => 'shawaashitsu', 'word_type' => 'Kata Benda', 'meaning' => 'Ruang Mandi'],
        ],
        'mobil' => [
            ['kanji' => '車', 'hiragana' => 'くるま', 'romaji' => 'kuruma', 'word_type' => 'Kata Benda', 'meaning' => 'Mobil'],
            ['kanji' => '自動車', 'hiragana' => 'じどうしゃ', 'romaji' => 'jidousha', 'word_type' => 'Kata Benda', 'meaning' => 'Kendaraan Roda Empat'],
        ],
        'sepeda' => [
            ['kanji' => '自転車', 'hiragana' => 'じてんしゃ', 'romaji' => 'jitensha', 'word_type' => 'Kata Benda', 'meaning' => 'Sepeda'],
        ],
        'motor' => [
            ['kanji' => 'バイク', 'hiragana' => 'ばいく', 'romaji' => 'baiku', 'word_type' => 'Kata Benda', 'meaning' => 'Sepeda Motor'],
            ['kanji' => 'オートバイ', 'hiragana' => 'おーとばい', 'romaji' => 'ootobai', 'word_type' => 'Kata Benda', 'meaning' => 'Motor'],
        ],
        'kereta' => [
            ['kanji' => '電車', 'hiragana' => 'でんしゃ', 'romaji' => 'densha', 'word_type' => 'Kata Benda', 'meaning' => 'Kereta Listrik'],
            ['kanji' => '新幹線', 'hiragana' => 'しんかんせん', 'romaji' => 'shinkansen', 'word_type' => 'Kata Benda', 'meaning' => 'Kereta Cepat Shinkansen'],
        ],
        'pesawat' => [
            ['kanji' => '飛行機', 'hiragana' => 'ひこうき', 'romaji' => 'hikouki', 'word_type' => 'Kata Benda', 'meaning' => 'Pesawat Terbang'],
        ],
        'buku' => [
            ['kanji' => '本', 'hiragana' => 'ほん', 'romaji' => 'hon', 'word_type' => 'Kata Benda', 'meaning' => 'Buku'],
        ],
        'kamus' => [
            ['kanji' => '辞書', 'hiragana' => 'じしょ', 'romaji' => 'jisho', 'word_type' => 'Kata Benda', 'meaning' => 'Kamus'],
        ],
        'kertas' => [
            ['kanji' => '紙', 'hiragana' => 'かみ', 'romaji' => 'kami', 'word_type' => 'Kata Benda', 'meaning' => 'Kertas'],
        ],
        'tas' => [
            ['kanji' => 'かばん', 'hiragana' => 'かばん', 'romaji' => 'kaban', 'word_type' => 'Kata Benda', 'meaning' => 'Tas'],
        ],
        'uang' => [
            ['kanji' => 'お金', 'hiragana' => 'おかね', 'romaji' => 'okane', 'word_type' => 'Kata Benda', 'meaning' => 'Uang'],
        ],
        'dompet' => [
            ['kanji' => '財布', 'hiragana' => 'さいふ', 'romaji' => 'saifu', 'word_type' => 'Kata Benda', 'meaning' => 'Dompet'],
        ],
        'air' => [
            ['kanji' => '水', 'hiragana' => 'みず', 'romaji' => 'mizu', 'word_type' => 'Kata Benda', 'meaning' => 'Air'],
            ['kanji' => 'お湯', 'hiragana' => 'おゆ', 'romaji' => 'oyu', 'word_type' => 'Kata Benda', 'meaning' => 'Air Panas'],
        ],
        'nasi' => [
            ['kanji' => 'ご飯', 'hiragana' => 'ごはん', 'romaji' => 'gohan', 'word_type' => 'Kata Benda', 'meaning' => 'Nasi / Makanan'],
        ],
        'makanan' => [
            ['kanji' => '食べ物', 'hiragana' => 'たべもの', 'romaji' => 'tabemono', 'word_type' => 'Kata Benda', 'meaning' => 'Makanan'],
            ['kanji' => '料理', 'hiragana' => 'りょうり', 'romaji' => 'ryouri', 'word_type' => 'Kata Benda', 'meaning' => 'Masakan'],
        ],
        'minuman' => [
            ['kanji' => '飲み物', 'hiragana' => 'のみもの', 'romaji' => 'nomimono', 'word_type' => 'Kata Benda', 'meaning' => 'Minuman'],
        ],
        'roti' => [
            ['kanji' => 'パン', 'hiragana' => 'ぱん', 'romaji' => 'pan', 'word_type' => 'Kata Benda', 'meaning' => 'Roti'],
        ],
        'daging' => [
            ['kanji' => '肉', 'hiragana' => 'にく', 'romaji' => 'niku', 'word_type' => 'Kata Benda', 'meaning' => 'Daging'],
            ['kanji' => '牛肉', 'hiragana' => 'ぎゅうにく', 'romaji' => 'gyuuniku', 'word_type' => 'Kata Benda', 'meaning' => 'Daging Sapi'],
            ['kanji' => '鶏肉', 'hiragana' => 'とりにく', 'romaji' => 'toriniku', 'word_type' => 'Kata Benda', 'meaning' => 'Daging Ayam'],
        ],
        'ikan' => [
            ['kanji' => '魚', 'hiragana' => 'さかな', 'romaji' => 'sakana', 'word_type' => 'Kata Benda', 'meaning' => 'Ikan'],
        ],
        'sayur' => [
            ['kanji' => '野菜', 'hiragana' => 'やさい', 'romaji' => 'yasai', 'word_type' => 'Kata Benda', 'meaning' => 'Sayuran'],
        ],
        'buah' => [
            ['kanji' => '果物', 'hiragana' => 'くだもの', 'romaji' => 'kudamono', 'word_type' => 'Kata Benda', 'meaning' => 'Buah-buahan'],
        ],
        'teh' => [
            ['kanji' => 'お茶', 'hiragana' => 'おちゃ', 'romaji' => 'ocha', 'word_type' => 'Kata Benda', 'meaning' => 'Teh Hijau Jepang'],
            ['kanji' => '紅茶', 'hiragana' => 'こうちゃ', 'romaji' => 'koucha', 'word_type' => 'Kata Benda', 'meaning' => 'Teh Hitam / Teh Manis'],
        ],
        'kopi' => [
            ['kanji' => 'コーヒー', 'hiragana' => 'こーひー', 'romaji' => 'koohii', 'word_type' => 'Kata Benda', 'meaning' => 'Kopi'],
        ],
        'susu' => [
            ['kanji' => '牛乳', 'hiragana' => 'ぎゅうにゅう', 'romaji' => 'gyuunyuu', 'word_type' => 'Kata Benda', 'meaning' => 'Susu Sapi'],
            ['kanji' => 'ミルク', 'hiragana' => 'みるく', 'romaji' => 'miruku', 'word_type' => 'Kata Benda', 'meaning' => 'Susu'],
        ],
        'teman' => [
            ['kanji' => '友達', 'hiragana' => 'ともだち', 'romaji' => 'tomodachi', 'word_type' => 'Kata Benda', 'meaning' => 'Teman / Kawan'],
        ],
        'keluarga' => [
            ['kanji' => '家族', 'hiragana' => 'かぞく', 'romaji' => 'kazoku', 'word_type' => 'Kata Benda', 'meaning' => 'Keluarga'],
        ],
        'ayah' => [
            ['kanji' => 'お父さん', 'hiragana' => 'おとうさん', 'romaji' => 'otousan', 'word_type' => 'Kata Benda', 'meaning' => 'Ayah (Orang Lain/Panggilan)'],
            ['kanji' => '父', 'hiragana' => 'ちち', 'romaji' => 'chichi', 'word_type' => 'Kata Benda', 'meaning' => 'Ayah Saya'],
        ],
        'ibu' => [
            ['kanji' => 'お母さん', 'hiragana' => 'おかあさん', 'romaji' => 'okaasan', 'word_type' => 'Kata Benda', 'meaning' => 'Ibu (Orang Lain/Panggilan)'],
            ['kanji' => '母', 'hiragana' => 'はは', 'romaji' => 'haha', 'word_type' => 'Kata Benda', 'meaning' => 'Ibu Saya'],
        ],
        'kakak laki-laki' => [
            ['kanji' => 'お兄さん', 'hiragana' => 'おにいさん', 'romaji' => 'oniisan', 'word_type' => 'Kata Benda', 'meaning' => 'Kakak Laki-laki'],
        ],
        'kakak perempuan' => [
            ['kanji' => 'お姉さん', 'hiragana' => 'おねえさん', 'romaji' => 'oneesan', 'word_type' => 'Kata Benda', 'meaning' => 'Kakak Perempuan'],
        ],
        'adik laki-laki' => [
            ['kanji' => '弟', 'hiragana' => 'おとうと', 'romaji' => 'otouto', 'word_type' => 'Kata Benda', 'meaning' => 'Adik Laki-laki'],
        ],
        'adik perempuan' => [
            ['kanji' => '妹', 'hiragana' => 'いもうと', 'romaji' => 'imouto', 'word_type' => 'Kata Benda', 'meaning' => 'Adik Perempuan'],
        ],
        'anak' => [
            ['kanji' => '子供', 'hiragana' => 'こども', 'romaji' => 'kodomo', 'word_type' => 'Kata Benda', 'meaning' => 'Anak-anak'],
        ],
        'orang' => [
            ['kanji' => '人', 'hiragana' => 'ひと', 'romaji' => 'hito', 'word_type' => 'Kata Benda', 'meaning' => 'Orang'],
            ['kanji' => '方', 'hiragana' => 'かた', 'romaji' => 'kata', 'word_type' => 'Kata Benda', 'meaning' => 'Beliau / Orang (Sopan)'],
        ],
        'jepang' => [
            ['kanji' => '日本', 'hiragana' => 'にほん', 'romaji' => 'nihon', 'word_type' => 'Kata Benda', 'meaning' => 'Negara Jepang'],
        ],
        'indonesia' => [
            ['kanji' => 'インドネシア', 'hiragana' => 'いんどねしあ', 'romaji' => 'indoneshia', 'word_type' => 'Kata Benda', 'meaning' => 'Negara Indonesia'],
        ],
        'hari ini' => [
            ['kanji' => '今日', 'hiragana' => 'きょう', 'romaji' => 'kyou', 'word_type' => 'Kata Benda', 'meaning' => 'Hari Ini'],
        ],
        'besok' => [
            ['kanji' => '明日', 'hiragana' => 'あした', 'romaji' => 'ashita', 'word_type' => 'Kata Benda', 'meaning' => 'Besok'],
        ],
        'kemarin' => [
            ['kanji' => '昨日', 'hiragana' => 'きのう', 'romaji' => 'kinou', 'word_type' => 'Kata Benda', 'meaning' => 'Kemarin'],
        ],
        'sekarang' => [
            ['kanji' => '今', 'hiragana' => 'いま', 'romaji' => 'ima', 'word_type' => 'Kata Keterangan', 'meaning' => 'Sekarang'],
        ],
        'pagi' => [
            ['kanji' => '朝', 'hiragana' => 'あさ', 'romaji' => 'asa', 'word_type' => 'Kata Benda', 'meaning' => 'Pagi Hari'],
        ],
        'siang' => [
            ['kanji' => '昼', 'hiragana' => 'ひる', 'romaji' => 'hiru', 'word_type' => 'Kata Benda', 'meaning' => 'Siang Hari'],
        ],
        'malam' => [
            ['kanji' => '夜', 'hiragana' => 'よる', 'romaji' => 'yoru', 'word_type' => 'Kata Benda', 'meaning' => 'Malam Hari'],
            ['kanji' => '晩', 'hiragana' => 'ばん', 'romaji' => 'ban', 'word_type' => 'Kata Benda', 'meaning' => 'Malam'],
        ],

        // ================= KATA KERJA (VERBS) =================
        'makan' => [
            ['kanji' => '食べる', 'hiragana' => 'たべる', 'romaji' => 'taberu', 'word_type' => 'Kata Kerja', 'meaning' => 'Makan'],
        ],
        'minum' => [
            ['kanji' => '飲む', 'hiragana' => 'のむ', 'romaji' => 'nomu', 'word_type' => 'Kata Kerja', 'meaning' => 'Minum'],
        ],
        'tidur' => [
            ['kanji' => '寝る', 'hiragana' => 'ねる', 'romaji' => 'neru', 'word_type' => 'Kata Kerja', 'meaning' => 'Tidur'],
        ],
        'bangun' => [
            ['kanji' => '起きる', 'hiragana' => 'おきる', 'romaji' => 'okiru', 'word_type' => 'Kata Kerja', 'meaning' => 'Bangun Tidur'],
        ],
        'pergi' => [
            ['kanji' => '行く', 'hiragana' => 'いく', 'romaji' => 'iku', 'word_type' => 'Kata Kerja', 'meaning' => 'Pergi'],
        ],
        'datang' => [
            ['kanji' => '来る', 'hiragana' => 'くる', 'romaji' => 'kuru', 'word_type' => 'Kata Kerja', 'meaning' => 'Datang'],
        ],
        'pulang' => [
            ['kanji' => '帰る', 'hiragana' => 'かえる', 'romaji' => 'kaeru', 'word_type' => 'Kata Kerja', 'meaning' => 'Pulang / Kembali'],
        ],
        'beli' => [
            ['kanji' => '買う', 'hiragana' => 'かう', 'romaji' => 'kau', 'word_type' => 'Kata Kerja', 'meaning' => 'Membeli'],
        ],
        'membeli' => [
            ['kanji' => '買う', 'hiragana' => 'かう', 'romaji' => 'kau', 'word_type' => 'Kata Kerja', 'meaning' => 'Membeli'],
        ],
        'jual' => [
            ['kanji' => '売る', 'hiragana' => 'うる', 'romaji' => 'uru', 'word_type' => 'Kata Kerja', 'meaning' => 'Menjual'],
        ],
        'menjual' => [
            ['kanji' => '売る', 'hiragana' => 'うる', 'romaji' => 'uru', 'word_type' => 'Kata Kerja', 'meaning' => 'Menjual'],
        ],
        'lihat' => [
            ['kanji' => '見る', 'hiragana' => 'みる', 'romaji' => 'miru', 'word_type' => 'Kata Kerja', 'meaning' => 'Melihat / Menonton'],
        ],
        'melihat' => [
            ['kanji' => '見る', 'hiragana' => 'みる', 'romaji' => 'miru', 'word_type' => 'Kata Kerja', 'meaning' => 'Melihat / Menonton'],
        ],
        'nonton' => [
            ['kanji' => '見る', 'hiragana' => 'みる', 'romaji' => 'miru', 'word_type' => 'Kata Kerja', 'meaning' => 'Menonton / Melihat'],
        ],
        'dengar' => [
            ['kanji' => '聞く', 'hiragana' => 'きく', 'romaji' => 'kiku', 'word_type' => 'Kata Kerja', 'meaning' => 'Mendengar / Bertanya'],
        ],
        'mendengar' => [
            ['kanji' => '聞く', 'hiragana' => 'きく', 'romaji' => 'kiku', 'word_type' => 'Kata Kerja', 'meaning' => 'Mendengarkan'],
        ],
        'baca' => [
            ['kanji' => '読む', 'hiragana' => 'よむ', 'romaji' => 'yomu', 'word_type' => 'Kata Kerja', 'meaning' => 'Membaca'],
        ],
        'membaca' => [
            ['kanji' => '読む', 'hiragana' => 'よむ', 'romaji' => 'yomu', 'word_type' => 'Kata Kerja', 'meaning' => 'Membaca'],
        ],
        'tulis' => [
            ['kanji' => '書く', 'hiragana' => 'かく', 'romaji' => 'kaku', 'word_type' => 'Kata Kerja', 'meaning' => 'Menulis'],
        ],
        'menulis' => [
            ['kanji' => '書く', 'hiragana' => 'かく', 'romaji' => 'kaku', 'word_type' => 'Kata Kerja', 'meaning' => 'Menulis'],
        ],
        'bicara' => [
            ['kanji' => '話す', 'hiragana' => 'はなす', 'romaji' => 'hanasu', 'word_type' => 'Kata Kerja', 'meaning' => 'Berbicara'],
        ],
        'berbicara' => [
            ['kanji' => '話す', 'hiragana' => 'はなす', 'romaji' => 'hanasu', 'word_type' => 'Kata Kerja', 'meaning' => 'Berbicara'],
        ],
        'belajar' => [
            ['kanji' => '勉強する', 'hiragana' => 'べんきょうする', 'romaji' => 'benkyousuru', 'word_type' => 'Kata Kerja', 'meaning' => 'Belajar'],
            ['kanji' => '習う', 'hiragana' => 'ならう', 'romaji' => 'narau', 'word_type' => 'Kata Kerja', 'meaning' => 'Belajar dari Guru'],
        ],
        'kerja' => [
            ['kanji' => '働く', 'hiragana' => 'はたらく', 'romaji' => 'hataraku', 'word_type' => 'Kata Kerja', 'meaning' => 'Bekerja'],
            ['kanji' => '仕事する', 'hiragana' => 'しごとする', 'romaji' => 'shigotosuru', 'word_type' => 'Kata Kerja', 'meaning' => 'Bekerja / Menjalankan Tugas'],
        ],
        'bekerja' => [
            ['kanji' => '働く', 'hiragana' => 'はたらく', 'romaji' => 'hataraku', 'word_type' => 'Kata Kerja', 'meaning' => 'Bekerja'],
        ],
        'mengajar' => [
            ['kanji' => '教える', 'hiragana' => 'おしえる', 'romaji' => 'oshieru', 'word_type' => 'Kata Kerja', 'meaning' => 'Mengajar / Memberitahu'],
        ],
        'membuat' => [
            ['kanji' => '作る', 'hiragana' => 'つくる', 'romaji' => 'tsukuru', 'word_type' => 'Kata Kerja', 'meaning' => 'Membuat / Memasak'],
        ],
        'pakai' => [
            ['kanji' => '使う', 'hiragana' => 'つかう', 'romaji' => 'tsukau', 'word_type' => 'Kata Kerja', 'meaning' => 'Menggunakan / Memakai'],
            ['kanji' => '着る', 'hiragana' => 'きる', 'romaji' => 'kiru', 'word_type' => 'Kata Kerja', 'meaning' => 'Memakai Baju'],
        ],
        'menggunakan' => [
            ['kanji' => '使う', 'hiragana' => 'つかう', 'romaji' => 'tsukau', 'word_type' => 'Kata Kerja', 'meaning' => 'Menggunakan'],
        ],
        'ambil' => [
            ['kanji' => '取る', 'hiragana' => 'とる', 'romaji' => 'toru', 'word_type' => 'Kata Kerja', 'meaning' => 'Mengambil'],
        ],
        'mengambil' => [
            ['kanji' => '取る', 'hiragana' => 'とる', 'romaji' => 'toru', 'word_type' => 'Kata Kerja', 'meaning' => 'Mengambil'],
        ],
        'tunggu' => [
            ['kanji' => '待つ', 'hiragana' => 'まつ', 'romaji' => 'matsu', 'word_type' => 'Kata Kerja', 'meaning' => 'Menunggu'],
        ],
        'menunggu' => [
            ['kanji' => '待つ', 'hiragana' => 'まつ', 'romaji' => 'matsu', 'word_type' => 'Kata Kerja', 'meaning' => 'Menunggu'],
        ],
        'bertemu' => [
            ['kanji' => '会う', 'hiragana' => 'あう', 'romaji' => 'au', 'word_type' => 'Kata Kerja', 'meaning' => 'Bertemu / Berjumpa'],
        ],
        'main' => [
            ['kanji' => '遊ぶ', 'hiragana' => 'あそぶ', 'romaji' => 'asobu', 'word_type' => 'Kata Kerja', 'meaning' => 'Bermain'],
        ],
        'bermain' => [
            ['kanji' => '遊ぶ', 'hiragana' => 'あそぶ', 'romaji' => 'asobu', 'word_type' => 'Kata Kerja', 'meaning' => 'Bermain'],
        ],
        'cuci' => [
            ['kanji' => '洗う', 'hiragana' => 'あらう', 'romaji' => 'arau', 'word_type' => 'Kata Kerja', 'meaning' => 'Mencuci'],
        ],
        'mencuci' => [
            ['kanji' => '洗う', 'hiragana' => 'あらう', 'romaji' => 'arau', 'word_type' => 'Kata Kerja', 'meaning' => 'Mencuci'],
        ],
        'masak' => [
            ['kanji' => '料理する', 'hiragana' => 'りょうりする', 'romaji' => 'ryourisuru', 'word_type' => 'Kata Kerja', 'meaning' => 'Memasak'],
            ['kanji' => '作る', 'hiragana' => 'つくる', 'romaji' => 'tsukuru', 'word_type' => 'Kata Kerja', 'meaning' => 'Membuat Makanan'],
        ],
        'memasak' => [
            ['kanji' => '料理する', 'hiragana' => 'りょうりする', 'romaji' => 'ryourisuru', 'word_type' => 'Kata Kerja', 'meaning' => 'Memasak'],
        ],
        'bantu' => [
            ['kanji' => '手伝う', 'hiragana' => 'てつだう', 'romaji' => 'tetsudau', 'word_type' => 'Kata Kerja', 'meaning' => 'Membantu'],
        ],
        'membantu' => [
            ['kanji' => '手伝う', 'hiragana' => 'てつだう', 'romaji' => 'tetsudau', 'word_type' => 'Kata Kerja', 'meaning' => 'Membantu'],
        ],
        'mengerti' => [
            ['kanji' => '分かる', 'hiragana' => 'わかる', 'romaji' => 'wakaru', 'word_type' => 'Kata Kerja', 'meaning' => 'Mengerti / Paham'],
        ],
        'paham' => [
            ['kanji' => '分かる', 'hiragana' => 'わかる', 'romaji' => 'wakaru', 'word_type' => 'Kata Kerja', 'meaning' => 'Mengerti / Paham'],
        ],
        'buka' => [
            ['kanji' => '開ける', 'hiragana' => 'あける', 'romaji' => 'akeru', 'word_type' => 'Kata Kerja', 'meaning' => 'Membuka'],
        ],
        'membuka' => [
            ['kanji' => '開ける', 'hiragana' => 'あける', 'romaji' => 'akeru', 'word_type' => 'Kata Kerja', 'meaning' => 'Membuka'],
        ],
        'tutup' => [
            ['kanji' => '閉める', 'hiragana' => 'しめる', 'romaji' => 'shimeru', 'word_type' => 'Kata Kerja', 'meaning' => 'Menutup'],
        ],
        'menutup' => [
            ['kanji' => '閉める', 'hiragana' => 'しめる', 'romaji' => 'shimeru', 'word_type' => 'Kata Kerja', 'meaning' => 'Menutup'],
        ],

        // ================= KATA SIFAT-I =================
        'besar' => [
            ['kanji' => '大きい', 'hiragana' => 'おおきい', 'romaji' => 'ookii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Besar'],
        ],
        'kecil' => [
            ['kanji' => '小さい', 'hiragana' => 'ちいさい', 'romaji' => 'chiisai', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Kecil'],
        ],
        'bagus' => [
            ['kanji' => '良い', 'hiragana' => 'いい', 'romaji' => 'ii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Bagus / Baik'],
        ],
        'baik' => [
            ['kanji' => '良い', 'hiragana' => 'いい', 'romaji' => 'ii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Baik / Bagus'],
        ],
        'jelek' => [
            ['kanji' => '悪い', 'hiragana' => 'わるい', 'romaji' => 'warui', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Jelek / Buruk'],
        ],
        'enak' => [
            ['kanji' => '美味しい', 'hiragana' => 'おいしい', 'romaji' => 'oishii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Enak / Lezat'],
            ['kanji' => 'うまい', 'hiragana' => 'うまい', 'romaji' => 'umai', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Enak (Kasual)'],
        ],
        'lezat' => [
            ['kanji' => '美味しい', 'hiragana' => 'おいしい', 'romaji' => 'oishii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Lezat / Enak'],
        ],
        'panas' => [
            ['kanji' => '暑い', 'hiragana' => 'あつい', 'romaji' => 'atsui', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Panas (Cuaca/Suhu)'],
            ['kanji' => '熱い', 'hiragana' => 'あつい', 'romaji' => 'atsui', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Panas (Benda/Air)'],
        ],
        'dingin' => [
            ['kanji' => '寒い', 'hiragana' => 'さむい', 'romaji' => 'samui', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Dingin (Cuaca/Suhu)'],
            ['kanji' => '冷たい', 'hiragana' => 'つめたい', 'romaji' => 'tsumetai', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Dingin (Benda/Minuman)'],
        ],
        'mahal' => [
            ['kanji' => '高い', 'hiragana' => 'たかい', 'romaji' => 'takai', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Mahal / Tinggi'],
        ],
        'tinggi' => [
            ['kanji' => '高い', 'hiragana' => 'たかい', 'romaji' => 'takai', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Tinggi'],
        ],
        'murah' => [
            ['kanji' => '安い', 'hiragana' => 'やすい', 'romaji' => 'yasui', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Murah'],
        ],
        'rendah' => [
            ['kanji' => '低い', 'hiragana' => 'ひくい', 'romaji' => 'hikui', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Rendah'],
        ],
        'baru' => [
            ['kanji' => '新しい', 'hiragana' => 'あたらしい', 'romaji' => 'atarashii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Baru'],
        ],
        'lama' => [
            ['kanji' => '古い', 'hiragana' => 'ふるい', 'romaji' => 'furui', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Lama / Kuno'],
        ],
        'tua' => [
            ['kanji' => '古い', 'hiragana' => 'ふるい', 'romaji' => 'furui', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Tua (Benda)'],
        ],
        'cepat' => [
            ['kanji' => '早い', 'hiragana' => 'はやい', 'romaji' => 'hayai', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Cepat / Pagi (Waktu)'],
            ['kanji' => '速い', 'hiragana' => 'はやい', 'romaji' => 'hayai', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Cepat (Kecepatan)'],
        ],
        'lambat' => [
            ['kanji' => '遅い', 'hiragana' => 'おそい', 'romaji' => 'osoi', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Lambat / Terlambat'],
        ],
        'mudah' => [
            ['kanji' => '易しい', 'hiragana' => 'やさしい', 'romaji' => 'yasashii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Mudah / Gampang'],
            ['kanji' => '簡単', 'hiragana' => 'かんたん', 'romaji' => 'kantan', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Sederhana / Mudah'],
        ],
        'gampang' => [
            ['kanji' => '簡単', 'hiragana' => 'かんたん', 'romaji' => 'kantan', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Gampang / Sederhana'],
        ],
        'sulit' => [
            ['kanji' => '難しい', 'hiragana' => 'むずかしい', 'romaji' => 'muzukashii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Sulit / Sukar'],
        ],
        'susah' => [
            ['kanji' => '難しい', 'hiragana' => 'むずかしい', 'romaji' => 'muzukashii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Susah / Sulit'],
        ],
        'sibuk' => [
            ['kanji' => '忙しい', 'hiragana' => 'いそがしい', 'romaji' => 'isogashii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Sibuk'],
        ],
        'menarik' => [
            ['kanji' => '面白い', 'hiragana' => 'おもしろい', 'romaji' => 'omoshiroi', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Menarik / Lucu'],
        ],
        'lucu' => [
            ['kanji' => '面白い', 'hiragana' => 'おもしろい', 'romaji' => 'omoshiroi', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Menarik / Lucu'],
            ['kanji' => '可愛い', 'hiragana' => 'かわいい', 'romaji' => 'kawaii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Imut / Lucu'],
        ],

        // ================= KATA SIFAT-NA =================
        'bersih' => [
            ['kanji' => '綺麗', 'hiragana' => 'きれい', 'romaji' => 'kirei', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Bersih / Cantik / Indah'],
        ],
        'cantik' => [
            ['kanji' => '綺麗', 'hiragana' => 'きれい', 'romaji' => 'kirei', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Cantik / Indah'],
        ],
        'ramai' => [
            ['kanji' => '賑やか', 'hiragana' => 'にぎやか', 'romaji' => 'nigiyaka', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Ramai / Meriah'],
        ],
        'sepi' => [
            ['kanji' => '静か', 'hiragana' => 'しずか', 'romaji' => 'shizuka', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Sepi / Hening / Tenang'],
        ],
        'tenang' => [
            ['kanji' => '静か', 'hiragana' => 'しずか', 'romaji' => 'shizuka', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Tenang / Sepi'],
        ],
        'terkenal' => [
            ['kanji' => '有名', 'hiragana' => 'ゆうめい', 'romaji' => 'yuumei', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Terkenal / Populer'],
        ],
        'ramah' => [
            ['kanji' => '親切', 'hiragana' => 'しんせつ', 'romaji' => 'shinsetsu', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Ramah / Baik Hati'],
        ],
        'sehat' => [
            ['kanji' => '元気', 'hiragana' => 'げんき', 'romaji' => 'genki', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Sehat / Semangat'],
        ],
        'praktis' => [
            ['kanji' => '便利', 'hiragana' => 'べんり', 'romaji' => 'benri', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Praktis / Bermanfaat'],
        ],
        'senggang' => [
            ['kanji' => '暇', 'hiragana' => 'ひま', 'romaji' => 'hima', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Waktu Luang / Senggang'],
        ],
        'luang' => [
            ['kanji' => '暇', 'hiragana' => 'ひま', 'romaji' => 'hima', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Waktu Luang'],
        ],
        'suka' => [
            ['kanji' => '好き', 'hiragana' => 'すき', 'romaji' => 'suki', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Suka'],
        ],
        'benci' => [
            ['kanji' => '嫌い', 'hiragana' => 'きらい', 'romaji' => 'kirai', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Benci / Tidak Suka'],
        ],
        'pintar' => [
            ['kanji' => '上手', 'hiragana' => 'じょうず', 'romaji' => 'jouzu', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Pandai / Mahir'],
            ['kanji' => '頭がいい', 'hiragana' => 'あたまがいい', 'romaji' => 'atama ga ii', 'word_type' => 'Kata Sifat-i', 'meaning' => 'Pintar / Cerdas'],
        ],
        'pandai' => [
            ['kanji' => '上手', 'hiragana' => 'じょうず', 'romaji' => 'jouzu', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Pandai / Mahir'],
        ],
        'aman' => [
            ['kanji' => '安全', 'hiragana' => 'あんぜん', 'romaji' => 'anzen', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Aman'],
        ],
        'bahaya' => [
            ['kanji' => '危険', 'hiragana' => 'きけん', 'romaji' => 'kiken', 'word_type' => 'Kata Sifat-na', 'meaning' => 'Bahaya'],
        ],

        // ================= UNGKAPAN / SALAM =================
        'terima kasih' => [
            ['kanji' => '', 'hiragana' => 'ありがとう', 'romaji' => 'arigatou', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Terima kasih (Kasual)'],
            ['kanji' => '', 'hiragana' => 'ありがとうございます', 'romaji' => 'arigatou gozaimasu', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Terima kasih banyak (Sopan)'],
        ],
        'makasih' => [
            ['kanji' => '', 'hiragana' => 'ありがとう', 'romaji' => 'arigatou', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Terima kasih'],
        ],
        'sama-sama' => [
            ['kanji' => '', 'hiragana' => 'どういたしまして', 'romaji' => 'douitashimashite', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Sama-sama'],
        ],
        'selamat pagi' => [
            ['kanji' => '', 'hiragana' => 'おはようございます', 'romaji' => 'ohayou gozaimasu', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Selamat Pagi'],
        ],
        'selamat siang' => [
            ['kanji' => '', 'hiragana' => 'こんにちは', 'romaji' => 'konnichiwa', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Selamat Siang / Halo'],
        ],
        'halo' => [
            ['kanji' => '', 'hiragana' => 'こんにちは', 'romaji' => 'konnichiwa', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Halo / Selamat Siang'],
        ],
        'selamat malam' => [
            ['kanji' => '', 'hiragana' => 'こんばんは', 'romaji' => 'konbanwa', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Selamat Malam'],
        ],
        'selamat tidur' => [
            ['kanji' => '', 'hiragana' => 'おやすみなさい', 'romaji' => 'oyasuminasai', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Selamat Tidur / Istirahat'],
        ],
        'permisi' => [
            ['kanji' => '', 'hiragana' => 'すみません', 'romaji' => 'sumimasen', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Permisi / Maaf'],
        ],
        'maaf' => [
            ['kanji' => '', 'hiragana' => 'ごめんなさい', 'romaji' => 'gomennasai', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Mohon Maaf'],
            ['kanji' => '', 'hiragana' => 'すみません', 'romaji' => 'sumimasen', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Maaf / Permisi'],
        ],
        'tolong' => [
            ['kanji' => '', 'hiragana' => 'おねがいします', 'romaji' => 'onegaishimasu', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Tolong / Mohon'],
        ],
        'sampai jumpa' => [
            ['kanji' => '', 'hiragana' => 'さようなら', 'romaji' => 'sayounara', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Selamat Tinggal'],
            ['kanji' => '', 'hiragana' => 'またね', 'romaji' => 'matane', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Sampai Jumpa (Santai)'],
        ],
        'semangat' => [
            ['kanji' => '', 'hiragana' => 'がんばってください', 'romaji' => 'ganbatte kudasai', 'word_type' => 'Ungkapan / Salam', 'meaning' => 'Semangat!'],
        ],
    ];

    /**
     * Search Japanese translation given Indonesian word.
     */
    public function search(string $indonesianTerm): array
    {
        $term = trim(mb_strtolower($indonesianTerm));
        if (empty($term)) {
            return [
                'found' => false,
                'kanji' => '',
                'hiragana' => '',
                'romaji' => '',
                'word_type' => '',
                'suggestions' => [],
            ];
        }

        $suggestions = [];

        // 1. Check local Vocabulary DB first
        $dbMatches = Vocabulary::where('meaning_id', 'like', "%{$term}%")
            ->orderByRaw("CASE WHEN LOWER(meaning_id) = ? THEN 1 ELSE 2 END", [$term])
            ->take(5)
            ->get();

        foreach ($dbMatches as $match) {
            $suggestions[] = [
                'kanji' => $match->kanji ?? '',
                'hiragana' => $match->hiragana,
                'romaji' => $match->romaji ?? '',
                'word_type' => $match->word_type ?? 'Kata Benda',
                'meaning' => $match->meaning_id,
                'source' => 'Database LPK',
            ];
        }

        // 2. Check built-in high-quality dictionary
        // Direct key lookup or partial match
        if (isset(self::$dictionary[$term])) {
            foreach (self::$dictionary[$term] as $item) {
                $suggestions[] = array_merge($item, ['source' => 'Kamus Resmi']);
            }
        } else {
            // Partial matching in dictionary keys
            foreach (self::$dictionary as $key => $items) {
                if (str_contains($key, $term) || str_contains($term, $key)) {
                    foreach ($items as $item) {
                        $suggestions[] = array_merge($item, ['source' => 'Kamus Resmi']);
                    }
                }
            }
        }

        // Deduplicate suggestions by kanji+hiragana
        $uniqueSuggestions = [];
        $seen = [];
        foreach ($suggestions as $s) {
            $key = ($s['kanji'] ?: $s['hiragana']) . '_' . $s['hiragana'];
            if (!isset($seen[$key])) {
                $seen[$key] = true;
                $uniqueSuggestions[] = $s;
            }
        }

        // If we found local matches, return the top match
        if (!empty($uniqueSuggestions)) {
            $top = $uniqueSuggestions[0];
            return [
                'found' => true,
                'kanji' => $top['kanji'],
                'hiragana' => $top['hiragana'],
                'romaji' => $top['romaji'],
                'word_type' => $top['word_type'] ?? 'Kata Benda',
                'suggestions' => array_slice($uniqueSuggestions, 0, 6),
            ];
        }

        // 3. Fallback to MyMemory Public Translation API
        try {
            $response = Http::timeout(3)->get('https://api.mymemory.translated.net/get', [
                'q' => $term,
                'langpair' => 'id|ja',
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $translatedText = $data['responseData']['translatedText'] ?? null;

                if (!empty($translatedText) && preg_match('/[\x{4E00}-\x{9FAF}\x{3040}-\x{309F}\x{30A0}-\x{30FF}]/u', $translatedText)) {
                    // Check if it has kanji vs hiragana/katakana
                    $isKanji = preg_match('/[\x{4E00}-\x{9FAF}]/u', $translatedText);
                    
                    return [
                        'found' => true,
                        'kanji' => $isKanji ? $translatedText : '',
                        'hiragana' => $isKanji ? '' : $translatedText,
                        'romaji' => '',
                        'word_type' => 'Kata Benda',
                        'suggestions' => [
                            [
                                'kanji' => $isKanji ? $translatedText : '',
                                'hiragana' => $isKanji ? '' : $translatedText,
                                'romaji' => '',
                                'word_type' => 'Kata Benda',
                                'meaning' => $term,
                                'source' => 'Online Translation',
                            ]
                        ],
                    ];
                }
            }
        } catch (\Throwable $e) {
            Log::info("MyMemory translate fallback error: " . $e->getMessage());
        }

        return [
            'found' => false,
            'kanji' => '',
            'hiragana' => '',
            'romaji' => '',
            'word_type' => '',
            'suggestions' => [],
        ];
    }

    /**
     * Alias for search: translate Indonesian meaning to Japanese kanji, hiragana, romaji.
     */
    public function translateIndonesianToJapanese(string $indonesianTerm): array
    {
        return $this->search($indonesianTerm);
    }

    /**
     * Fetch authentic Japanese pronunciation MP3 audio binary.
     */
    public function getNativePronunciationAudio(string $text): ?string
    {
        $clean = trim($text);
        if (empty($clean)) {
            return null;
        }

        $url = 'https://translate.google.com/translate_tts?ie=UTF-8&tl=ja&client=tw-ob&q=' . urlencode($clean);

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Referer' => 'https://translate.google.com/',
            ])->timeout(6)->get($url);

            if ($response->successful() && strlen($response->body()) > 200) {
                return $response->body();
            }
        } catch (\Throwable $e) {
            Log::warning("TTS Audio fetch failed: " . $e->getMessage());
        }

        return null;
    }

    /**
     * Save authentic native Japanese pronunciation audio file to public storage disk.
     */
    public function saveNativePronunciationFile(string $text): ?string
    {
        $audioBinary = $this->getNativePronunciationAudio($text);
        if (!$audioBinary) {
            return null;
        }

        $fileName = 'native_' . md5($text) . '_' . time() . '.mp3';
        $relativeDir = 'lms/vocab_audios';
        $fullPath = $relativeDir . '/' . $fileName;

        Storage::disk('public')->put($fullPath, $audioBinary);

        return '/storage/' . $fullPath;
    }
}
