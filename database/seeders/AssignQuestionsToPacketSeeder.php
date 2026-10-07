<?php

namespace Database\Seeders;

use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\Chapter;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssignQuestionsToPacketSeeder extends Seeder
{
    /**
     * Assign semua soal yang ada ke satu paket: N4 > Bab 1 > Moji-Goi
     * Agar tampil terkelompok di Bank Soal Index.
     */
    public function run(): void
    {
        // Cari/buat kategori Moji-Goi
        $mojiGoi = QuestionCategory::where('section_type', 'moji_goi')->first();
        if (!$mojiGoi) {
            $mojiGoi = QuestionCategory::create([
                'name'         => 'Moji-Goi (Kosakata & Kanji)',
                'code'         => 'MG',
                'section_type' => 'moji_goi',
                'description'  => 'Soal kosakata, cara baca kanji, dan penulisan.',
            ]);
        }

        // Cari kategori Choukai
        $choukai = QuestionCategory::where('section_type', 'choukai')->first();
        if (!$choukai) {
            $choukai = QuestionCategory::create([
                'name'         => 'Mendengarkan (Choukai)',
                'code'         => 'CK',
                'section_type' => 'choukai',
                'description'  => 'Soal audio listening dan pemahaman percakapan.',
            ]);
        }

        // Cari/buat kategori Bunpou
        $bunpou = QuestionCategory::where('section_type', 'bunpou')->first();
        if (!$bunpou) {
            $bunpou = QuestionCategory::create([
                'name'         => 'Tata Bahasa (Bunpou)',
                'code'         => 'BP',
                'section_type' => 'bunpou',
                'description'  => 'Soal pola kalimat, partikel, dan tata bahasa.',
            ]);
        }

        // Cari Bab 1 atau Bab pertama yang ada di database
        $chapter1 = Chapter::where('chapter_number', 1)->first() ?? Chapter::first();

        // Ambil semua soal yang ada di database untuk dimasukkan ke 1 paket
        $allQuestions = Question::all();

        $this->command->info("Menemukan {$allQuestions->count()} soal untuk di-assign ke paket.");

        foreach ($allQuestions as $q) {
            // Tentukan kategori berdasarkan audio_url (choukai) atau default moji_goi
            $catId = $mojiGoi->id;
            if ($q->audio_url) {
                $catId = $choukai->id;
                $sectionType = 'choukai';
            } else {
                $sectionType = $mojiGoi->section_type;
            }

            $q->update([
                'chapter_id'           => $chapter1?->id,
                'question_category_id' => $catId,
                'section_type'         => $sectionType,
                'level'                => 'N4',
                'level_code'           => 'N4',
            ]);
        }

        // Update level_code di semua soal
        DB::statement("UPDATE questions SET level_code = level WHERE level_code IS NULL OR level_code = ''");

        $this->command->info("✅ Selesai! Semua soal di-assign ke paket N4 > Bab 1.");

        // Tampilkan ringkasan
        $summary = Question::with(['category', 'chapter'])
            ->select('level', 'chapter_id', 'question_category_id', DB::raw('count(*) as total'))
            ->groupBy('level', 'chapter_id', 'question_category_id')
            ->get();

        foreach ($summary as $row) {
            $this->command->line(
                "Level: {$row->level} | Chapter: " . ($row->chapter?->title ?? 'Tanpa Bab') .
                " | Kategori: " . ($row->category?->name ?? 'N/A') .
                " | Total: {$row->total} soal"
            );
        }
    }
}
