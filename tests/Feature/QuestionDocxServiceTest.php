<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionCategory;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\User;
use App\Services\QuestionDocxService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class QuestionDocxServiceTest extends TestCase
{
    use RefreshDatabase;

    protected User $sensei;
    protected Subject $subject;
    protected QuestionBank $package;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'sensei']);
        Role::firstOrCreate(['name' => 'admin']);

        $this->sensei = User::factory()->create();
        $this->sensei->assignRole('sensei');

        $this->subject = Subject::create([
            'name' => 'Bahasa Jepang N4',
            'code' => 'JPN-N4',
            'order_index' => 1,
            'is_active' => true,
            'created_by' => $this->sensei->id,
        ]);

        $this->package = QuestionBank::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->sensei->id,
            'title' => 'Master Bank Soal N4 Tryout A',
            'code' => 'QB-N4-TRY-A',
            'level' => 'N4',
            'duration_minutes' => 60,
            'passing_score' => 90,
            'max_score' => 180,
            'display_mode' => 'formal',
            'is_active' => true,
        ]);

        QuestionCategory::create([
            'name' => 'Kosakata Harian N4',
            'code' => 'IND-GOI-N4',
            'section_type' => 'moji_goi',
            'level' => 'N4',
        ]);
    }

    public function test_sensei_can_download_official_word_template(): void
    {
        $response = $this->actingAs($this->sensei)
            ->get(route('sensei.questions.template.download'));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-type'), 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') ||
            str_contains($response->headers->get('content-disposition'), 'Template_Soal_CBT_LPK.docx')
        );
    }

    public function test_sensei_can_export_package_to_docx(): void
    {
        // 1. Tambahkan 1 soal manual
        $q = Question::create([
            'created_by' => $this->sensei->id,
            'level' => 'N4',
            'section_type' => 'bunpou',
            'instruction' => '( ) に なにを いれますか。',
            'question_text' => 'わたしは まいにch にほんご ( ) べんきょうします。',
            'score_points' => 1.0,
            'explanation' => 'Partikel を menandai objek kalimat.',
        ]);

        QuestionOption::create([
            'question_id' => $q->id,
            'option_key' => 'A',
            'option_text' => 'を',
            'is_correct' => true,
        ]);

        QuestionOption::create([
            'question_id' => $q->id,
            'option_key' => 'B',
            'option_text' => 'に',
            'is_correct' => false,
        ]);

        $this->package->questions()->attach($q->id, [
            'section_type' => 'bunpou',
            'order_index' => 1,
        ]);

        $response = $this->actingAs($this->sensei)
            ->get(route('sensei.questions.export-docx', $this->package->id));

        $response->assertStatus(200);
        $this->assertTrue(
            str_contains($response->headers->get('content-disposition'), 'Naskah_Soal_') ||
            str_contains($response->headers->get('content-type'), 'wordprocessingml')
        );
    }

    public function test_sensei_can_import_docx_template_and_create_questions_in_database(): void
    {
        // 1. Generate template resmi docx
        $docxService = new QuestionDocxService();
        $tempPath = $docxService->generateTemplate();

        $this->assertFileExists($tempPath);

        $uploadedFile = new UploadedFile(
            $tempPath,
            'Template_Soal_CBT_LPK.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        $response = $this->actingAs($this->sensei)
            ->post(route('sensei.questions.import-docx', $this->package->id), [
                'file' => $uploadedFile,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // Verifikasi bahwa 4 soal dari template berhasil masuk ke tabel questions dan pivot
        $this->assertEquals(4, $this->package->questions()->count());

        $firstQuestion = $this->package->questions()->first();
        $this->assertNotNull($firstQuestion);
        $this->assertEquals(4, $firstQuestion->options()->count());

        $correctOption = $firstQuestion->options()->where('is_correct', true)->first();
        $this->assertNotNull($correctOption);
        $this->assertEquals('A', $correctOption->option_key);

        if (file_exists($tempPath)) {
            @unlink($tempPath);
        }
    }

    public function test_sensei_can_import_questions_via_quick_text(): void
    {
        $rawText = "1. わたしは まいにち (____) を べんきょうします。\nA. にほんご\nB. えいご\nC. フランスご\nD. ドイツご\nKUNCI: A\nPEMBAHASAN: にほんご artinya bahasa Jepang.\n\n2. ここ (____) ねこが います。\nA. で\nB. に\nC. を\nD. へ\nKUNCI: B\nPEMBAHASAN: Partikel に untuk keberadaan makhluk hidup (います).";

        $response = $this->actingAs($this->sensei)
            ->post(route('sensei.questions.import-text', $this->package->id), [
                'raw_text' => $rawText,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $this->assertEquals(2, $this->package->questions()->count());

        $first = $this->package->questions()->first();
        $this->assertStringContainsString('わたしは まいにち (____) を べんきょうします。', $first->question_text);
        $this->assertEquals('A', $first->options()->where('is_correct', true)->value('option_key'));

        $second = $this->package->questions()->skip(1)->first();
        $this->assertStringContainsString('ここ (____) ねこが います。', $second->question_text);
        $this->assertEquals('B', $second->options()->where('is_correct', true)->value('option_key'));
    }

    public function test_sensei_can_import_contoh_10_soal_pure_cbt_docx(): void
    {
        $filePath = base_path('Soal/Contoh_10_Soal_JLPT_N4_Template.docx');
        $this->assertFileExists($filePath);

        $uploadedFile = new UploadedFile(
            $filePath,
            'Contoh_10_Soal_JLPT_N4_Template.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        $response = $this->actingAs($this->sensei)
            ->post(route('sensei.questions.import-docx', $this->package->id), [
                'file' => $uploadedFile,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        // 10 soal harus berhasil diimpor
        $this->assertEquals(10, $this->package->questions()->count());

        $questions = $this->package->questions()->with('options')->get();

        // Verifikasi semua soal murni CBT: tidak ada explanation/pembahasan
        foreach ($questions as $q) {
            $this->assertEmpty($q->explanation, "Soal ID {$q->id} tidak boleh memiliki pembahasan (Pure CBT).");
            $this->assertEquals(4, $q->options->count(), "Soal ID {$q->id} harus memiliki 4 pilihan.");
            $this->assertEquals(1, $q->options->where('is_correct', true)->count(), "Soal ID {$q->id} harus memiliki tepat 1 kunci.");
        }

        // Soal #1: Moji-Goi Kanji -> Hiragana
        $q1 = $questions->first();
        $this->assertEquals('moji_goi', $q1->section_type);
        $this->assertStringContainsString('新聞を', $q1->question_text);
        $this->assertEquals('A', $q1->options->where('is_correct', true)->first()->option_key);
        $this->assertNotNull($q1->question_category_id, 'Soal #1 harus terhubung ke Indikator Capaian N4 moji_goi.');

        // Soal #7 & #8: Dokkai dengan wacana
        $q7 = $questions[6];
        $this->assertEquals('dokkai', $q7->section_type);
        $this->assertNotEmpty($q7->reading_passage);
        $this->assertStringContainsString('カリナ', $q7->reading_passage);

        // Soal #10: Bergambar (meja dan buku)
        $q10 = $questions[9];
        $this->assertNotEmpty($q10->image_url, 'Soal #10 harus memiliki gambar meja dan buku.');
        $this->assertEquals('A', $q10->options->where('is_correct', true)->first()->option_key);
    }

    public function test_docx_importer_supports_dynamic_options_5_options_and_2_options(): void
    {
        $bunpouCat = QuestionCategory::create([
            'name' => 'Tata Bahasa N4 Lanjutan',
            'code' => 'IND-BUN-ADV',
            'section_type' => 'bunpou',
            'level' => 'N4',
        ]);

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $phpWord->addSection();

        // 1. Soal dengan 5 OPSI (A s/d E, Kunci: E) + Uji Baris Indikator Eksplisit
        $t1 = $section->addTable();
        $t1->addRow()->addCell(8500)->addText('SOAL NO: 1 | SEKSI: bunpou');
        $t1->addRow()->addCell(2000)->addText('INDIKATOR');
        $t1->addCell(6000)->addText('Tata Bahasa N4 Lanjutan');
        $t1->addRow()->addCell(2000)->addText('SOAL');
        $t1->addCell(6000)->addText('Pilihlah salah satu dari 5 pilihan berikut:');
        $t1->addRow()->addCell(2000)->addText('OPSI A');
        $t1->addCell(6000)->addText('Pilihan Satu');
        $t1->addRow()->addCell(2000)->addText('OPSI B');
        $t1->addCell(6000)->addText('Pilihan Dua');
        $t1->addRow()->addCell(2000)->addText('OPSI C');
        $t1->addCell(6000)->addText('Pilihan Tiga');
        $t1->addRow()->addCell(2000)->addText('OPSI D');
        $t1->addCell(6000)->addText('Pilihan Empat');
        $t1->addRow()->addCell(2000)->addText('OPSI E');
        $t1->addCell(6000)->addText('Pilihan Lima (Benar)');
        $t1->addRow()->addCell(2000)->addText('KUNCI JAWABAN');
        $t1->addCell(6000)->addText('E');

        $section->addTextBreak(1);

        // 2. Soal dengan 2 OPSI (A dan B, Benar / Salah, Kunci: B) - Fallback Indikator Otomatis
        $t2 = $section->addTable();
        $t2->addRow()->addCell(8500)->addText('SOAL NO: 2 | SEKSI: moji_goi');
        $t2->addRow()->addCell(2000)->addText('SOAL');
        $t2->addCell(6000)->addText('Tokyo adalah ibukota Jepang (Maru / Batsu)?');
        $t2->addRow()->addCell(2000)->addText('OPSI A');
        $t2->addCell(6000)->addText('○ (Benar / Maru)');
        $t2->addRow()->addCell(2000)->addText('OPSI B');
        $t2->addCell(6000)->addText('× (Salah / Batsu)');
        $t2->addRow()->addCell(2000)->addText('KUNCI JAWABAN');
        $t2->addCell(6000)->addText('B');

        $tempDocx = storage_path('app/temp_test_dynamic_options.docx');
        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($tempDocx);

        $uploadedFile = new UploadedFile(
            $tempDocx,
            'dynamic_options.docx',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        $response = $this->actingAs($this->sensei)
            ->post(route('sensei.questions.import-docx', $this->package->id), [
                'file' => $uploadedFile,
            ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect();

        $questions = $this->package->questions()->with('options')->get();
        $this->assertEquals(2, $questions->count());

        // Cek Soal 1: 5 opsi (A-E), kunci E, terhubung ke Indikator eksplisit
        $q5 = $questions->first();
        $this->assertEquals(5, $q5->options->count());
        $this->assertEquals(['A', 'B', 'C', 'D', 'E'], $q5->options->pluck('option_key')->toArray());
        $this->assertEquals('E', $q5->options->where('is_correct', true)->first()->option_key);
        $this->assertEquals($bunpouCat->id, $q5->question_category_id, 'Soal 1 harus terhubung ke indikator eksplisit.');

        // Cek Soal 2: 2 opsi (A-B), kunci B, terhubung ke fallback Indikator moji_goi N4
        $q2 = $questions->skip(1)->first();
        $this->assertEquals(2, $q2->options->count());
        $this->assertEquals(['A', 'B'], $q2->options->pluck('option_key')->toArray());
        $this->assertEquals('B', $q2->options->where('is_correct', true)->first()->option_key);
        $this->assertNotNull($q2->question_category_id, 'Soal 2 harus otomatis fallback ke indikator moji_goi N4.');

        if (file_exists($tempDocx)) {
            @unlink($tempDocx);
        }
    }

    public function test_export_package_with_svg_image_and_reimport_roundtrip(): void
    {
        // Buat soal dengan gambar SVG dan audio
        $q = Question::create([
            'created_by' => $this->sensei->id,
            'level' => 'N4',
            'section_type' => 'bunpou',
            'instruction' => 'Petunjuk soal bergambar SVG',
            'question_text' => 'Perhatikan gambar rambu keselamatan berikut:',
            'image_url' => '/storage/cbt/images/sign_safety.svg',
            'score_points' => 2.0,
        ]);

        QuestionOption::create([
            'question_id' => $q->id,
            'option_key' => 'A',
            'option_text' => 'Rambu Keselamatan Kerja',
            'is_correct' => true,
        ]);
        QuestionOption::create([
            'question_id' => $q->id,
            'option_key' => 'B',
            'option_text' => 'Rambu Lalu Lintas',
            'is_correct' => false,
        ]);

        $this->package->questions()->attach($q->id, [
            'section_type' => 'bunpou',
            'order_index' => 1,
        ]);

        $service = new QuestionDocxService();
        $exportedFile = $service->exportPackageToDocx($this->package);

        $this->assertFileExists($exportedFile);

        // Uji re-import naskah yang diekspor ke package baru
        $newPackage = QuestionBank::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->sensei->id,
            'title' => 'Target Re-import Package',
            'code' => 'QB-REIMPORT-TEST',
            'level' => 'N4',
            'duration_minutes' => 60,
            'passing_score' => 90,
            'is_active' => true,
        ]);

        $uploadedFile = new UploadedFile(
            $exportedFile,
            basename($exportedFile),
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            null,
            true
        );

        $result = $service->importFromDocx($uploadedFile, $newPackage, $this->sensei->id);

        $this->assertTrue($result['success']);
        $this->assertEquals(1, $result['imported_count']);
        $this->assertEquals(0, $result['failed_count']);

        $importedQ = $newPackage->questions()->first();
        $this->assertNotNull($importedQ);
        $this->assertStringContainsString('Perhatikan gambar rambu keselamatan berikut:', $importedQ->question_text);
        $this->assertEquals(2, $importedQ->options()->count());
        $this->assertEquals('A', $importedQ->options()->where('is_correct', true)->first()->option_key);

        if (file_exists($exportedFile)) {
            @unlink($exportedFile);
        }
    }
}

