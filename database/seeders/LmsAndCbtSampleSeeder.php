<?php

namespace Database\Seeders;

use App\Models\Batch;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Exam;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionOption;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Database\Seeder;

class LmsAndCbtSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $sensei = User::where('email', 'sensei@gmail.com')->first();
        $siswa = User::where('email', 'siswa@gmail.com')->first();

        // 1. Buat Contoh Batch / Angkatan
        $batch = Batch::firstOrCreate(
            ['code' => 'B15-KG-2026'],
            [
                'name' => 'Batch 15 - Tokutei Ginou Kaigo',
                'program_type' => 'tokutei_ginou',
                'target_level' => 'N4',
                'start_date' => now()->subMonths(1),
                'end_date' => now()->addMonths(5),
                'status' => 'active',
                'notes' => 'Target kelulusan JFT-Basic A2 & Skill Kaigo Prometric.',
            ]
        );

        if ($sensei) {
            $batch->users()->syncWithoutDetaching([
                $sensei->id => ['role_in_batch' => 'sensei'],
            ]);
        }
        if ($siswa) {
            $batch->users()->syncWithoutDetaching([
                $siswa->id => ['role_in_batch' => 'siswa'],
            ]);
        }

        // 2. Buat Contoh Kursus N4
        $course = Course::firstOrCreate(
            ['slug' => 'n4-minna-no-nihongo-ii'],
            [
                'created_by' => $sensei?->id,
                'title' => 'Bahasa Jepang N4 (Minna no Nihongo Chuukyuu / Bab 26-50)',
                'level' => 'N4',
                'description' => 'Materi persiapan resmi standar JLPT N4 dan JFT-Basic A2 untuk program Tokutei Ginou & Magang.',
                'is_published' => true,
                'order_index' => 1,
            ]
        );

        // 3. Buat Bab 26
        $chapter26 = Chapter::firstOrCreate(
            [
                'course_id' => $course->id,
                'chapter_number' => 26,
            ],
            [
                'title' => '第26課 (Bab 26): Bentuk ~んです (~ndesu) & Permintaan Tolong',
                'description' => 'Memahami penggunaan ungkapan penjelas alasan dan meminta bantuan secara sopan.',
                'order_index' => 26,
                'is_published' => true,
            ]
        );

        // 4. Buat Sub-Materi Pelajaran Bab 26
        Lesson::firstOrCreate(
            [
                'chapter_id' => $chapter26->id,
                'title' => 'Pola Tata Bahasa: ~んです / ~んですが',
            ],
            [
                'content_type' => 'text_grammar',
                'content_body' => '<p class="text-base text-slate-700 leading-relaxed mb-4">Pola <strong>〜んです (~ndesu)</strong> digunakan saat pembicara ingin memberikan alasan, latar belakang keadaan, atau meminta penjelasan yang lebih mendalam dari lawan bicara.</p>
                <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl my-4">
                    <p class="font-bold text-amber-900">Rumus Pembentukan:</p>
                    <p class="text-amber-800">Bentuk Biasa (Futsuukei) + んです</p>
                    <p class="text-sm text-amber-700 mt-1">*Untuk Kata Benda (N) & Kata Sifat-Na (Na-Adj) bentuk sekarang positif: ditambah な (contoh: 暇なんです / 休みなんです)</p>
                </div>
                <h4 class="font-bold text-slate-800 text-lg mt-6 mb-2">Contoh Kalimat (例文):</h4>
                <ul class="space-y-3">
                    <li class="bg-white p-3 rounded-lg border border-slate-200">
                        <span class="text-japan-red font-semibold">A: </span>どうして <ruby>遅<rt>おく</rt></ruby>れたんですか。<br>
                        <span class="text-japan-slate text-sm">Kenapa Anda terlambat?</span><br>
                        <span class="text-japan-red font-semibold">B: </span>バスが <ruby>来<rt>こ</rt></ruby>なかったんです。<br>
                        <span class="text-japan-slate text-sm">Karena busnya tidak datang.</span>
                    </li>
                </ul>',
                'duration_minutes' => 15,
                'order_index' => 1,
                'is_published' => true,
            ]
        );

        // 5. Buat Kosakata (Kotoba) Bab 26
        $vocabSample = [
            ['kanji' => '見ます', 'hiragana' => 'みます', 'romaji' => 'mimasu', 'meaning_id' => 'memeriksa, mengecek', 'word_type' => 'verb_2'],
            ['kanji' => '探します', 'hiragana' => 'さがします', 'romaji' => 'sagashimasu', 'meaning_id' => 'mencari', 'word_type' => 'verb_1'],
            ['kanji' => '遅れます', 'hiragana' => 'おくれます', 'romaji' => 'okuremasu', 'meaning_id' => 'terlambat (waktu)', 'word_type' => 'verb_2'],
            ['kanji' => '間に合います', 'hiragana' => 'まにあいます', 'romaji' => 'maniaimasu', 'meaning_id' => 'keburu, tepat waktu', 'word_type' => 'verb_1'],
            ['kanji' => 'ごみ', 'hiragana' => 'ごみ', 'romaji' => 'gomi', 'meaning_id' => 'sampah', 'word_type' => 'noun'],
        ];

        foreach ($vocabSample as $idx => $voc) {
            Vocabulary::firstOrCreate(
                [
                    'chapter_id' => $chapter26->id,
                    'hiragana' => $voc['hiragana'],
                ],
                [
                    'kanji' => $voc['kanji'],
                    'romaji' => $voc['romaji'],
                    'meaning_id' => $voc['meaning_id'],
                    'word_type' => $voc['word_type'],
                    'order_index' => $idx + 1,
                ]
            );
        }

        // 6. Buat Kategori Soal CBT
        $catMoji = QuestionCategory::firstOrCreate(
            ['name' => 'Gengo Chishiki - Moji & Goi (Huruf & Kosakata)'],
            ['section_type' => 'moji_goi', 'level' => 'N4', 'description' => 'Soal cara baca kanji, penulisan kanji, dan kosakata konteks N4.']
        );
        $catBunpou = QuestionCategory::firstOrCreate(
            ['name' => 'Gengo Chishiki - Bunpou & Dokkai (Tata Bahasa & Bacaan)'],
            ['section_type' => 'bunpou', 'level' => 'N4', 'description' => 'Soal pola kalimat, partikel, menyusun kalimat bintang, dan bacaan.']
        );
        $catChoukai = QuestionCategory::firstOrCreate(
            ['name' => 'Choukai (Listening Comprehension)'],
            ['section_type' => 'choukai', 'level' => 'N4', 'description' => 'Soal mendengarkan audio situasi kerja dan percakapan harian.']
        );

        // 7. Buat Contoh Soal-Soal N4
        // Soal 1: Moji-Goi (Kanji Yomikata)
        $q1 = Question::firstOrCreate(
            ['question_text' => 'きのう、あたらしい <ruby>病院<rt>＿＿＿</rt></ruby>へ いきました。'],
            [
                'created_by' => $sensei?->id,
                'question_category_id' => $catMoji->id,
                'level' => 'N4',
                'section_type' => 'moji_goi',
                'instruction' => '＿＿＿の ことばは どう よみますか。１・２・３・４から いちばん いい ものを ひとつ えらんで ください。',
                'explanation' => 'Kata 「病院」 dibaca 「びょういん (byouin)」 yang berarti rumah sakit. Huruf 「病」 dibaca びょう (panjang) dan 「院」 dibaca いん.',
                'score_points' => 2.00,
            ]
        );
        $this->createOptions($q1, [
            ['key' => 'A', 'text' => 'びょういん', 'correct' => true],
            ['key' => 'B', 'text' => 'びょいん', 'correct' => false],
            ['key' => 'C', 'text' => 'びょうえん', 'correct' => false],
            ['key' => 'D', 'text' => 'びょえん', 'correct' => false],
        ]);

        // Soal 2: Bunpou (Pola Kalimat)
        $q2 = Question::firstOrCreate(
            ['question_text' => 'あしたは <ruby>試験<rt>しけん</rt></ruby>が あるので、きょうは 早く <ruby>帰<rt>かえ</rt></ruby>って ＿＿＿＿＿ んです。'],
            [
                'created_by' => $sensei?->id,
                'question_category_id' => $catBunpou->id,
                'level' => 'N4',
                'section_type' => 'bunpou',
                'instruction' => '（　　）に なにを 入れますか。A・B・C・Dから いちばん いい ものを ひとつ えらんで ください。',
                'explanation' => 'Sebelum pola penjelas alasan 「〜んです」, kata kerja menggunakan Bentuk Biasa (Futsuukei). Bentuk ingin (〜たい) futsuukei-nya adalah 〜たい. Jadi yang benar: 勉強したい (ingin belajar).',
                'score_points' => 2.00,
            ]
        );
        $this->createOptions($q2, [
            ['key' => 'A', 'text' => '勉強します', 'correct' => false],
            ['key' => 'B', 'text' => '勉強したい', 'correct' => true],
            ['key' => 'C', 'text' => '勉強して', 'correct' => false],
            ['key' => 'D', 'text' => '勉強したの', 'correct' => false],
        ]);

        // Soal 3: Choukai / Listening
        $q3 = Question::firstOrCreate(
            ['question_text' => '男の 人と 女の 人が 話して います。男の 人は 何を 持って いきますか。'],
            [
                'created_by' => $sensei?->id,
                'question_category_id' => $catChoukai->id,
                'level' => 'N4',
                'section_type' => 'choukai',
                'instruction' => 'まず 話を きいて ください。それから、ただしい こたえを えらんで ください。',
                'audio_url' => null, // Placeholder audio
                'audio_play_limit' => 1,
                'explanation' => 'Pada percakapan, perempuan meminta laki-laki membawa payung karena ramalan cuaca sore akan hujan.',
                'score_points' => 3.00,
            ]
        );
        $this->createOptions($q3, [
            ['key' => 'A', 'text' => 'かさ (Payung)', 'correct' => true],
            ['key' => 'B', 'text' => 'ほん (Buku)', 'correct' => false],
            ['key' => 'C', 'text' => 'カメラ (Kamera)', 'correct' => false],
            ['key' => 'D', 'text' => 'さいふ (Dompet)', 'correct' => false],
        ]);

        // 8. Buat Paket Ujian CBT Simulasi N4
        $exam = Exam::firstOrCreate(
            ['code' => 'SIM-N4-01'],
            [
                'created_by' => $sensei?->id,
                'batch_id' => $batch->id,
                'title' => 'Simulasi Resmi JLPT N4 / JFT-Basic A2 (Paket Tryout 01)',
                'exam_type' => 'jlpt_simulation',
                'level' => 'N4',
                'description' => 'Simulasi penuh standar kelulusan resmi N4. Ujian terdiri dari sesi Moji-Goi, Bunpou-Dokkai, dan Choukai.',
                'duration_minutes' => 60,
                'passing_score' => 90,
                'max_score' => 180,
                'max_attempts' => 2,
                'start_time' => now()->subDays(1),
                'end_time' => now()->addDays(30),
                'is_published' => true,
                'display_mode' => 'formal',
                'allow_student_mode_switch' => false,
                'allow_review_immediately' => true,
                'is_randomized' => false,
            ]
        );

        // Hubungkan Soal ke Paket Ujian
        $exam->questions()->syncWithoutDetaching([
            $q1->id => ['section_type' => 'moji_goi', 'order_index' => 1],
            $q2->id => ['section_type' => 'bunpou', 'order_index' => 2],
            $q3->id => ['section_type' => 'choukai', 'order_index' => 3],
        ]);
    }

    private function createOptions(Question $question, array $options): void
    {
        foreach ($options as $opt) {
            QuestionOption::firstOrCreate(
                [
                    'question_id' => $question->id,
                    'option_key' => $opt['key'],
                ],
                [
                    'option_text' => $opt['text'],
                    'is_correct' => $opt['correct'],
                ]
            );
        }
    }
}
