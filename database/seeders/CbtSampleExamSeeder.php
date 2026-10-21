<?php

namespace Database\Seeders;

use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionCategory;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CbtSampleExamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan direktori publik untuk CBT audio & gambar tersedia
        $audioDir = public_path('storage/cbt/audios');
        $imageDir = public_path('storage/cbt/images');

        File::ensureDirectoryExists($audioDir);
        File::ensureDirectoryExists($imageDir);

        // 2. Buat / Generate 5 Gambar Ilustrasi Soal CBT
        $images = [
            'sign_safety.svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 300" width="500" height="300"><rect width="500" height="300" rx="20" fill="#1e293b"/><rect x="20" y="20" width="460" height="260" rx="15" fill="#0f172a" stroke="#22c55e" stroke-width="4"/><circle cx="250" cy="110" r="50" fill="#22c55e"/><path d="M230 110 L245 125 L275 95" stroke="#ffffff" stroke-width="8" fill="none" stroke-linecap="round"/><text x="250" y="200" fill="#ffffff" font-family="sans-serif" font-size="28" font-weight="bold" text-anchor="middle">安 全 第 一</text><text x="250" y="235" fill="#94a3b8" font-family="sans-serif" font-size="16" text-anchor="middle">ヘルメットと保護メガネを着用してください</text></svg>',
            'station_map.svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 300" width="500" height="300"><rect width="500" height="300" rx="20" fill="#1e293b"/><rect x="20" y="20" width="460" height="260" rx="15" fill="#0f172a" stroke="#3b82f6" stroke-width="4"/><rect x="50" y="100" width="160" height="100" rx="10" fill="#1e3a8a" stroke="#60a5fa" stroke-width="2"/><text x="130" y="155" fill="#ffffff" font-family="sans-serif" font-size="22" font-weight="bold" text-anchor="middle">北 口 (Kita-guchi)</text><rect x="290" y="100" width="160" height="100" rx="10" fill="#831843" stroke="#f472b6" stroke-width="2"/><text x="370" y="155" fill="#ffffff" font-family="sans-serif" font-size="22" font-weight="bold" text-anchor="middle">南 口 (Minami-guchi)</text><path d="M210 150 L290 150" stroke="#f59e0b" stroke-width="6" stroke-dasharray="8 8"/><text x="250" y="70" fill="#f59e0b" font-family="sans-serif" font-size="20" font-weight="bold" text-anchor="middle">駅の改札口 (Pintu Tiket Stasiun)</text></svg>',
            'trash_sort.svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 300" width="500" height="300"><rect width="500" height="300" rx="20" fill="#1e293b"/><rect x="40" y="70" width="120" height="160" rx="12" fill="#dc2626" stroke="#f87171" stroke-width="3"/><text x="100" y="145" fill="#ffffff" font-family="sans-serif" font-size="16" font-weight="bold" text-anchor="middle">燃えるゴミ</text><text x="100" y="175" fill="#fee2e2" font-family="sans-serif" font-size="12" text-anchor="middle">(Organik/Bakar)</text><rect x="190" y="70" width="120" height="160" rx="12" fill="#2563eb" stroke="#60a5fa" stroke-width="3"/><text x="250" y="145" fill="#ffffff" font-family="sans-serif" font-size="16" font-weight="bold" text-anchor="middle">燃えないゴミ</text><text x="250" y="175" fill="#dbeafe" font-family="sans-serif" font-size="12" text-anchor="middle">(Non-Bakar/Kaca)</text><rect x="340" y="70" width="120" height="160" rx="12" fill="#16a34a" stroke="#4ade80" stroke-width="3"/><text x="400" y="145" fill="#ffffff" font-family="sans-serif" font-size="16" font-weight="bold" text-anchor="middle">資源ゴミ</text><text x="400" y="175" fill="#dcfce7" font-family="sans-serif" font-size="12" text-anchor="middle">(Botol Plastik/Kaleng)</text><text x="250" y="45" fill="#facc15" font-family="sans-serif" font-size="18" font-weight="bold" text-anchor="middle">ゴミの分別ルール (Aturan Pemilahan Sampah)</text></svg>',
            'clock_time.svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 300" width="500" height="300"><rect width="500" height="300" rx="20" fill="#1e293b"/><circle cx="250" cy="150" r="90" fill="#0f172a" stroke="#e2e8f0" stroke-width="6"/><line x1="250" y1="150" x2="250" y2="85" stroke="#f43f5e" stroke-width="6" stroke-linecap="round"/><line x1="250" y1="150" x2="310" y2="150" stroke="#38bdf8" stroke-width="8" stroke-linecap="round"/><circle cx="250" cy="150" r="8" fill="#ffffff"/><text x="250" y="75" fill="#ffffff" font-family="sans-serif" font-size="14" font-weight="bold" text-anchor="middle">12</text><text x="325" y="155" fill="#ffffff" font-family="sans-serif" font-size="14" font-weight="bold" text-anchor="middle">3</text><text x="250" y="230" fill="#ffffff" font-family="sans-serif" font-size="14" font-weight="bold" text-anchor="middle">6</text><text x="175" y="155" fill="#ffffff" font-family="sans-serif" font-size="14" font-weight="bold" text-anchor="middle">9</text><text x="250" y="275" fill="#facc15" font-family="sans-serif" font-size="16" font-weight="bold" text-anchor="middle">面接集合時間: 3:00 PM (15:00)</text></svg>',
            'kaigo_transfer.svg' => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 300" width="500" height="300"><rect width="500" height="300" rx="20" fill="#1e293b"/><rect x="20" y="20" width="460" height="260" rx="15" fill="#0f172a" stroke="#a855f7" stroke-width="4"/><circle cx="170" cy="110" r="30" fill="#c084fc"/><rect x="150" y="145" width="40" height="70" rx="8" fill="#9333ea"/><circle cx="310" cy="110" r="30" fill="#60a5fa"/><rect x="290" y="145" width="40" height="70" rx="8" fill="#2563eb"/><path d="M190 160 Q 240 130 290 160" stroke="#facc15" stroke-width="5" fill="none" stroke-linecap="round"/><text x="250" y="245" fill="#ffffff" font-family="sans-serif" font-size="20" font-weight="bold" text-anchor="middle">介護: ベッドから車椅子への移乗介助</text><text x="250" y="270" fill="#cbd5e1" font-family="sans-serif" font-size="13" text-anchor="middle">Bantuan Memindahkan Pasien ke Kursi Roda</text></svg>',
        ];

        foreach ($images as $filename => $svgContent) {
            File::put($imageDir . '/' . $filename, $svgContent);
        }

        // 3. Buat 5 File Audio WAV untuk Soal Choukai (Listening)
        $sampleWav = function($freq = 440, $duration = 3.0) {
            $sampleRate = 8000;
            $numSamples = (int) ($sampleRate * $duration);
            $byteRate = $sampleRate * 1;
            $blockAlign = 1;
            $dataSize = $numSamples * 1;
            $chunkSize = 36 + $dataSize;

            $header = "RIFF" . pack("V", $chunkSize) . "WAVEfmt " .
                pack("V", 16) . pack("v", 1) . pack("v", 1) . pack("V", $sampleRate) .
                pack("V", $byteRate) . pack("v", $blockAlign) . pack("v", 8) .
                "data" . pack("V", $dataSize);

            $data = '';
            for ($i = 0; $i < $numSamples; $i++) {
                $t = $i / $sampleRate;
                $val = (int)(127 + 60 * sin(2 * M_PI * $freq * $t) * (1 - $t/$duration));
                $data .= chr($val);
            }
            return $header . $data;
        };

        $audios = [
            'choukai_1.wav' => $sampleWav(440, 4.0),
            'choukai_2.wav' => $sampleWav(523, 4.0),
            'choukai_3.wav' => $sampleWav(587, 4.0),
            'choukai_4.wav' => $sampleWav(659, 4.0),
            'choukai_5.wav' => $sampleWav(698, 4.0),
        ];

        foreach ($audios as $filename => $wavContent) {
            File::put($audioDir . '/' . $filename, $wavContent);
        }

        // 4. Kategori Soal
        $catMoji = QuestionCategory::firstOrCreate(['name' => 'Moji-Goi (Kosakata & Kanji)'], ['section_type' => 'moji_goi', 'level' => 'N4', 'description' => 'Kosakata, Kanji, dan Makna Kata']);
        $catBunpou = QuestionCategory::firstOrCreate(['name' => 'Tata Bahasa (Bunpou)'], ['section_type' => 'bunpou', 'level' => 'N4', 'description' => 'Pola Kalimat dan Partikel']);
        $catDokkai = QuestionCategory::firstOrCreate(['name' => 'Membaca (Dokkai)'], ['section_type' => 'dokkai', 'level' => 'N4', 'description' => 'Pemahaman Wacana dan Teks']);
        $catChoukai = QuestionCategory::firstOrCreate(['name' => 'Mendengarkan (Choukai)'], ['section_type' => 'choukai', 'level' => 'N4', 'description' => 'Pemahaman Audio Percakapan']);

        $sensei = User::role('sensei')->first() ?? User::first();

        // 5. Buat 10 Soal Pilihan Ganda Standar (Kosakata & Tata Bahasa)
        $standardQuestionsData = [
            [
                'cat' => $catMoji,
                'instruction' => '【問題 1】 下線の 言葉は どう 書きますか。１・２・３・４から いちばん いい ものを ひとつ えらんで ください。',
                'text' => 'きのう <ruby>病院<rt>びょういん</rt></ruby>へ 行って、<ruby>薬<rt>くすり</rt></ruby>を <u>のみました</u>。',
                'explanation' => '「のみました」 adalah bentuk lampau dari kata kerja 飲みます (minum). Kanji yang benar adalah 飲みました.',
                'options' => [
                    ['key' => 'A', 'text' => '飲みました', 'is_correct' => true],
                    ['key' => 'B', 'text' => '食べました', 'is_correct' => false],
                    ['key' => 'C' , 'text' => '休みました', 'is_correct' => false],
                    ['key' => 'D', 'text' => '住みました', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catMoji,
                'instruction' => '【問題 2】 漢字の 読み方を １・２・３・４から えらんで ください。',
                'text' => '来週から 日本の 工場で <u>研修</u>が 始まります。',
                'explanation' => '研修 dibaca けんしゅう (kenshuu) yang berarti pelatihan/training magang di Jepang.',
                'options' => [
                    ['key' => 'A', 'text' => 'けんきゅう', 'is_correct' => false],
                    ['key' => 'B', 'text' => 'けんしゅう', 'is_correct' => true],
                    ['key' => 'C', 'text' => 'こうしゅう', 'is_correct' => false],
                    ['key' => 'D', 'text' => 'あんない', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catBunpou,
                'instruction' => '【問題 3】 （ ）には 何を 入れますか。１・２・３・４から いちばん いい ものを ひとつ えらんで ください。',
                'text' => '熱が ありますから、今日は 早く 帰って（ ）。',
                'explanation' => 'Pola ~てもいいですか digunakan untuk meminta izin ("Bolehkah saya pulang lebih cepat?").',
                'options' => [
                    ['key' => 'A', 'text' => 'も いいですか', 'is_correct' => true],
                    ['key' => 'B', 'text' => 'は いけません', 'is_correct' => false],
                    ['key' => 'C', 'text' => 'て ください', 'is_correct' => false],
                    ['key' => 'D', 'text' => 'なければなりません', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catBunpou,
                'instruction' => '【問題 4】 （ ）に 入る 正しい 助詞を えらんで ください。',
                'text' => 'わたしは 日本（ ）車や 機械の 技術を 勉強したいです。',
                'explanation' => 'Partikel で menunjukkan tempat terjadinya aktivitas belajar teknik di Jepang.',
                'options' => [
                    ['key' => 'A', 'text' => 'へ', 'is_correct' => false],
                    ['key' => 'B', 'text' => 'に', 'is_correct' => false],
                    ['key' => 'C', 'text' => 'で', 'is_correct' => true],
                    ['key' => 'D', 'text' => 'を', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catBunpou,
                'instruction' => '【問題 5】 正しい 文法表現を えらんで ください。',
                'text' => '危ないですから、機械に 手を（ ）。',
                'explanation' => 'Pola ~てはいけません menyatakan larangan keras ("Dilarang menyentuh mesin"). Bentuk te dari 触ります adalah 触って.',
                'options' => [
                    ['key' => 'A', 'text' => '触っても いいです', 'is_correct' => false],
                    ['key' => 'B', 'text' => '触っては いけません', 'is_correct' => true],
                    ['key' => 'C', 'text' => '触ることが できます', 'is_correct' => false],
                    ['key' => 'D', 'text' => '触らなければなりません', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catMoji,
                'instruction' => '【問題 6】 （ ）に 入る 最も 適切な 言葉を えらんで ください。',
                'text' => '毎朝 仕事が 始まる 前に、全員で（ ）を します。',
                'explanation' => '朝礼 (chourei) adalah apel pagi / briefing kerja yang wajib diadakan di perusahaan Jepang.',
                'options' => [
                    ['key' => 'A', 'text' => '朝礼（ちょうれい）', 'is_correct' => true],
                    ['key' => 'B', 'text' => '残業（ざんぎょう）', 'is_correct' => false],
                    ['key' => 'C', 'text' => '散歩（さんぽ）', 'is_correct' => false],
                    ['key' => 'D', 'text' => '旅行（りょこう）', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catBunpou,
                'instruction' => '【問題 7】 文法形式の 選択',
                'text' => 'パスポートを（ ）ことが ありますか。',
                'explanation' => 'Pola ~た ことが あります menyatakan pengalaman di masa lalu. Bentuk Ta dari 見せます adalah 見せた.',
                'options' => [
                    ['key' => 'A', 'text' => '見せる', 'is_correct' => false],
                    ['key' => 'B', 'text' => '見せた', 'is_correct' => true],
                    ['key' => 'C', 'text' => '見せて', 'is_correct' => false],
                    ['key' => 'D', 'text' => '見せよう', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catDokkai,
                'instruction' => '【問題 8】 メモを 読んで、質問に 答えて ください。',
                'text' => '【メモ】<br>「山田さんへ：今日の ミーティングは １４時から ２階の 第１会議室で 行います。資料を ５部 コピーして 持ってきて ください。 佐藤より」<br><br><b>質問：山田さんは 何を しなければなりませんか。</b>',
                'explanation' => 'Berdasarkan memo, Sato meminta Yamada memfotokopi dokumen sebanyak 5 rangkap dan membawanya ke rapat.',
                'options' => [
                    ['key' => 'A', 'text' => '資料を ５部 コピーして 会議室へ 持っていく。', 'is_correct' => true],
                    ['key' => 'B', 'text' => '１５時に ２階の 部屋に 行く。', 'is_correct' => false],
                    ['key' => 'C', 'text' => '佐藤さんに 電話を かける。', 'is_correct' => false],
                    ['key' => 'D', 'text' => 'ミーティングを キャンセルする。', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catBunpou,
                'instruction' => '【問題 9】 条件形（〜たら）の 選択',
                'text' => '日本へ（ ）、まず 市役所で 住民登録を します。',
                'explanation' => 'Pola ~たら (pengandaian/sekuensial waktu): 日本へ 行ったら (Setelah tiba di Jepang...).',
                'options' => [
                    ['key' => 'A', 'text' => '行ったら', 'is_correct' => true],
                    ['key' => 'B', 'text' => '行くなら', 'is_correct' => false],
                    ['key' => 'C', 'text' => '行けば', 'is_correct' => false],
                    ['key' => 'D', 'text' => '行っても', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catMoji,
                'instruction' => '【問題 10】 意味が いちばん 近い ものを えらんで ください。',
                'text' => '彼は とても <u>まじめな</u> 人です。',
                'explanation' => 'まじめ (rajin, sungguh-sungguh, tekun dalam bekerja).',
                'options' => [
                    ['key' => 'A', 'text' => 'うそを つかないで、一生懸命 働く', 'is_correct' => true],
                    ['key' => 'B', 'text' => 'いつも 怒っている', 'is_correct' => false],
                    ['key' => 'C', 'text' => '背が とても 高い', 'is_correct' => false],
                    ['key' => 'D', 'text' => '日本語が 全然 話せない', 'is_correct' => false],
                ]
            ],
        ];

        // 6. Buat 5 Soal dengan VOICE / AUDIO (Choukai)
        $audioQuestionsData = [
            [
                'cat' => $catChoukai,
                'instruction' => '【聴解 問題 1】 音声を 聞いて、男の人が これから 何を するか えらんで ください。',
                'text' => '<b>【音声会話】</b><br>女：田中さん、この 書類を ３階の 事務所へ 届けて くれませんか。<br>男：はい、分かりました。すぐ 持って いきます。<br><br><b>質問：男の人は これから 何を しますか。</b>',
                'audio' => '/storage/cbt/audios/choukai_1.wav',
                'explanation' => 'Pria menyetujui permintaan wanita untuk mengantarkan dokumen ke kantor lantai 3.',
                'options' => [
                    ['key' => 'A', 'text' => '３階の 事務所へ 書類を 持っていく。', 'is_correct' => true],
                    ['key' => 'B', 'text' => '書類を ゴミ箱に 捨てる。', 'is_correct' => false],
                    ['key' => 'C', 'text' => '１階へ 降りて 昼ごはんを 食べる。', 'is_correct' => false],
                    ['key' => 'D', 'text' => '田中さんに 電話を かける。', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catChoukai,
                'instruction' => '【聴解 問題 2】 音声を 聞いて、２人が 何時に 会うか えらんで ください。',
                'text' => '<b>【音声会話】</b><br>女：明日の 集合時間は 何時ですか。<br>男：駅前に 朝 ８時半に 集まりましょう。<br>女：分かりました。８時３０分ですね。<br><br><b>質問：２人は 明日 何時に 集まりますか。</b>',
                'audio' => '/storage/cbt/audios/choukai_2.wav',
                'explanation' => 'Percakapan memastikan waktu kumpul adalah pukul 08:30 pagi (８時半).',
                'options' => [
                    ['key' => 'A', 'text' => '朝 ８時', 'is_correct' => false],
                    ['key' => 'B', 'text' => '朝 ８時３０分（８時半）', 'is_correct' => true],
                    ['key' => 'C', 'text' => '朝 ９時', 'is_correct' => false],
                    ['key' => 'D', 'text' => '夕方 ５時', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catChoukai,
                'instruction' => '【聴解 問題 3】 音声を 聞いて、現場の 注意事項を えらんで ください。',
                'text' => '<b>【工場内アナウンス】</b><br>「作業中は 必ず ヘルメットと 安全靴を 着用して ください。安全第一で 作業を 行いましょう。」<br><br><b>質問：作業員は何を 必ず 着用しなければなりませんか。</b>',
                'audio' => '/storage/cbt/audios/choukai_3.wav',
                'explanation' => 'Instruksi keselamatan mewajibkan pemakaian helm (ヘルメット) dan sepatu keselamatan (安全靴).',
                'options' => [
                    ['key' => 'A', 'text' => 'ヘルメットと 安全靴', 'is_correct' => true],
                    ['key' => 'B', 'text' => 'サンダルと 帽子', 'is_correct' => false],
                    ['key' => 'C', 'text' => 'スーツと ネクタイ', 'is_correct' => false],
                    ['key' => 'D', 'text' => 'マスクと サングラス', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catChoukai,
                'instruction' => '【聴解 問題 4】 音声を 聞いて、正しい 交通手段を えらんで ください。',
                'text' => '<b>【音声会話】</b><br>男：会社まで どうやって 来ますか。<br>女：家から 駅まで 自転車で 行って、そこから 電車で ２０分です。<br><br><b>質問：女の人は 会社まで 何で 行きますか。</b>',
                'audio' => '/storage/cbt/audios/choukai_4.wav',
                'explanation' => 'Wanita naik sepeda ke stasiun lalu naik kereta ke kantor.',
                'options' => [
                    ['key' => 'A', 'text' => '自転車と 電車', 'is_correct' => true],
                    ['key' => 'B', 'text' => '車と バス', 'is_correct' => false],
                    ['key' => 'C', 'text' => '歩いて 行く', 'is_correct' => false],
                    ['key' => 'D', 'text' => 'タクシーだけ', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catChoukai,
                'instruction' => '【聴解 問題 5】 音声を 聞いて、病気の 症状を えらんで ください。',
                'text' => '<b>【病院での会話】</b><br>医者：どうしましたか。<br>患者：昨日の 夜から 頭が 痛くて、熱も ３８度 あります。<br><br><b>質問：患者の 症状は どれですか。</b>',
                'audio' => '/storage/cbt/audios/choukai_5.wav',
                'explanation' => 'Gejala pasien: sakit kepala (頭が痛い) dan demam 38 derajat (熱がある).',
                'options' => [
                    ['key' => 'A', 'text' => '頭が 痛くて 熱が ある', 'is_correct' => true],
                    ['key' => 'B', 'text' => 'お腹が 痛い', 'is_correct' => false],
                    ['key' => 'C', 'text' => '足の 骨が 折れた', 'is_correct' => false],
                    ['key' => 'D', 'text' => '目が 見えない', 'is_correct' => false],
                ]
            ],
        ];

        // 7. Buat 5 Soal dengan GAMBAR / ILUSTRASI (Visual Diagram)
        $imageQuestionsData = [
            [
                'cat' => $catMoji,
                'instruction' => '【画像問題 1】 下の 看板の 画像を 見て、質問に 答えて ください。',
                'text' => '<b>質問：この 工場の 看板には 何と 書いて ありますか。</b>',
                'image' => '/storage/cbt/images/sign_safety.svg',
                'explanation' => 'Tulisan kanji pada rambu keselamatan hijau adalah 安全第一 (Anzen Daiichi - Utamakan Keselamatan).',
                'options' => [
                    ['key' => 'A', 'text' => '安全第一（あんぜんだいいち）', 'is_correct' => true],
                    ['key' => 'B', 'text' => '立入禁止（たちいりきんし）', 'is_correct' => false],
                    ['key' => 'C', 'text' => '駐車禁止（ちゅうしゃきんし）', 'is_correct' => false],
                    ['key' => 'D', 'text' => '非常出口（ひじょうでぐち）', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catDokkai,
                'instruction' => '【画像問題 2】 駅の 案内図を 見て、正しい 説明を えらんで ください。',
                'text' => '<b>質問：青い 看板の 出口は どちらですか。</b>',
                'image' => '/storage/cbt/images/station_map.svg',
                'explanation' => 'Berdasarkan denah stasiun, pintu keluar biru di sebelah kiri bertuliskan 北口 (Kita-guchi / Pintu Utara).',
                'options' => [
                    ['key' => 'A', 'text' => '北口（きたぐち）', 'is_correct' => true],
                    ['key' => 'B', 'text' => '南口（みなみぐち）', 'is_correct' => false],
                    ['key' => 'C', 'text' => '東口（ひがしぐち）', 'is_correct' => false],
                    ['key' => 'D', 'text' => '西口（にしぐち）', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catMoji,
                'instruction' => '【画像問題 3】 ゴミ箱の イラストを 見て、ペットボトルは どこに 捨てますか。',
                'text' => '<b>質問：空の ペットボトル（Plastik Botol）は どの ゴミ箱に 捨てますか。</b>',
                'image' => '/storage/cbt/images/trash_sort.svg',
                'explanation' => 'Botol plastik dan kaleng termasuk kategori 資源ゴミ (Sampah daur ulang) di kotak hijau.',
                'options' => [
                    ['key' => 'A', 'text' => '緑色の 資源ゴミ箱（しげんゴミ）', 'is_correct' => true],
                    ['key' => 'B', 'text' => '赤色の 燃えるゴミ箱', 'is_correct' => false],
                    ['key' => 'C', 'text' => '青色の 燃えないゴミ箱', 'is_correct' => false],
                    ['key' => 'D', 'text' => '川に 投げる', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catDokkai,
                'instruction' => '【画像問題 4】 時計の イラストを 見て、質問に 答えて ください。',
                'text' => '<b>質問：企業面接の 集合時間は 何時ですか。</b>',
                'image' => '/storage/cbt/images/clock_time.svg',
                'explanation' => 'Jam dinding menunjukkan pukul 3 tepat sore hari (午後３時 / 15:00).',
                'options' => [
                    ['key' => 'A', 'text' => '午後３時（１５時）', 'is_correct' => true],
                    ['key' => 'B', 'text' => '午前１２時', 'is_correct' => false],
                    ['key' => 'C', 'text' => '午後６時', 'is_correct' => false],
                    ['key' => 'D', 'text' => '午前９時', 'is_correct' => false],
                ]
            ],
            [
                'cat' => $catDokkai,
                'instruction' => '【画像問題 5】 介護現場の イラストを 見て、介助の 種類を えらんで ください。',
                'text' => '<b>質問：イラストの 介護技術は 何の 介助ですか。</b>',
                'image' => '/storage/cbt/images/kaigo_transfer.svg',
                'explanation' => 'Gambar mengilustrasikan teknik memindahkan pasien dari tempat tidur ke kursi roda (移乗介助 / Ijou Kaijo).',
                'options' => [
                    ['key' => 'A', 'text' => 'ベッドから 車椅子への 移乗介助', 'is_correct' => true],
                    ['key' => 'B', 'text' => '食事の 介助', 'is_correct' => false],
                    ['key' => 'C', 'text' => '入浴の 介助', 'is_correct' => false],
                    ['key' => 'D', 'text' => '排泄の 介助', 'is_correct' => false],
                ]
            ],
        ];

        // 8. Buat Mata Pelajaran & Master Bank Soal (QuestionBank)
        $subject = Subject::firstOrCreate(
            ['code' => 'JPN-N4'],
            [
                'name' => 'Bahasa Jepang Standar N4',
                'description' => 'Mata pelajaran persiapan kerja dan ujian JLPT N4 / JFT-Basic A2.',
                'order_index' => 1,
                'is_active' => true,
                'created_by' => $sensei->id,
            ]
        );

        $questionBank = QuestionBank::firstOrCreate(
            ['code' => 'BNK-N4-SIMULASI-01'],
            [
                'subject_id' => $subject->id,
                'created_by' => $sensei->id,
                'title' => 'Paket Master Simulasi JLPT N4 (Sampel Lengkap)',
                'level' => 'N4',
                'description' => 'Master Bank Soal N4: 10 Soal Moji-Goi & Bunpou, 5 Soal Listening (Choukai), 5 Soal Bergambar.',
                'duration_minutes' => 45,
                'passing_score' => 100,
                'max_score' => 180,
                'is_active' => true,
            ]
        );

        // 9. Buat Paket Ujian CBT Resmi
        $exam = Exam::updateOrCreate(
            ['code' => 'CBT-N4-MULTIMEDIA-2026'],
            [
                'created_by' => $sensei->id,
                'subject_id' => $subject->id,
                'question_bank_id' => $questionBank->id,
                'title' => 'Simulasi JLPT N4 & JFT-Basic (Uji Coba Lengkap: Audio & Gambar)',
                'level' => 'N4',
                'exam_type' => 'jlpt_simulation',
                'description' => 'Paket Ujian CBT Lengkap: 10 Soal Moji-Goi & Bunpou, 5 Soal Listening Audio (Choukai), dan 5 Soal Bergambar (Denah, K3, & Situasi Kerja).',
                'duration_minutes' => 45,
                'passing_score' => 100,
                'max_score' => 180,
                'max_attempts' => 3,
                'is_published' => true,
                'display_mode' => 'formal',
                'allow_student_mode_switch' => false,
                'is_randomized' => false,
                'allow_review_immediately' => true,
            ]
        );

        $allQuestions = [];

        // Helper untuk memasukkan soal dan opsi
        $insertQuestionGroup = function($list, $type) use ($sensei, &$allQuestions) {
            foreach ($list as $item) {
                $q = Question::create([
                    'created_by' => $sensei->id,
                    'question_category_id' => $item['cat']->id,
                    'level' => 'N4',
                    'level_code' => 'N4',
                    'section_type' => $item['cat']->section_type,
                    'instruction' => $item['instruction'],
                    'question_text' => $item['text'],
                    'image_url' => $item['image'] ?? null,
                    'audio_url' => $item['audio'] ?? null,
                    'audio_play_limit' => 2,
                    'explanation' => $item['explanation'] ?? null,
                    'score_points' => 9.0, // 20 soal * 9 = 180 poin max score
                ]);

                foreach ($item['options'] as $opt) {
                    QuestionOption::create([
                        'question_id' => $q->id,
                        'option_key' => $opt['key'],
                        'option_text' => $opt['text'],
                        'is_correct' => $opt['is_correct'],
                    ]);
                }

                $allQuestions[] = $q->id;
            }
        };

        // Masukkan 10 PG Standar, 5 Audio, 5 Gambar
        $insertQuestionGroup($standardQuestionsData, 'standard');
        $insertQuestionGroup($audioQuestionsData, 'audio');
        $insertQuestionGroup($imageQuestionsData, 'image');

        // Hubungkan 20 Soal ke Master Bank Soal & Paket Ujian CBT
        $syncData = [];
        $questionsObj = Question::whereIn('id', $allQuestions)->get()->keyBy('id');
        foreach ($allQuestions as $index => $qId) {
            $qObj = $questionsObj->get($qId);
            $syncData[$qId] = [
                'section_type' => $qObj?->section_type ?? 'bunpou',
                'order_index' => $index + 1,
            ];
        }

        $questionBank->questions()->sync($syncData);
        $exam->questions()->sync($syncData);
    }
}
