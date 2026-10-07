<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SenseiQuestionBankAndExamDecouplingTest extends TestCase
{
    use RefreshDatabase;

    protected User $sensei;
    protected Subject $subject;

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
    }

    public function test_question_bank_and_exam_are_decoupled_when_exam_is_deleted(): void
    {
        // 1. Create a Master Question Bank in /sensei/questions
        $bank = QuestionBank::create([
            'subject_id' => $this->subject->id,
            'created_by' => $this->sensei->id,
            'title' => 'Master Bank Soal JLPT N4 Paket A',
            'code' => 'QB-N4-01',
            'level' => 'N4',
            'duration_minutes' => 60,
            'passing_score' => 90,
            'max_score' => 180,
            'display_mode' => 'formal',
            'is_active' => true,
        ]);

        // 2. Add questions to the Question Bank
        $q1 = Question::create([
            'created_by' => $this->sensei->id,
            'level' => 'N4',
            'section_type' => 'bunpou',
            'question_text' => 'これは本です。',
            'score_points' => 2.0,
        ]);
        $bank->questions()->attach($q1->id, ['section_type' => 'bunpou', 'order_index' => 1]);

        $this->assertEquals(1, $bank->questions()->count());

        // 3. Create a Scheduled Exam in /sensei/exams linked to this Question Bank
        $response = $this->actingAs($this->sensei)->post(route('sensei.exams.store'), [
            'title' => 'Simulasi CBT N4 Angkatan 12',
            'code' => 'EXAM-CBT-12',
            'question_bank_id' => $bank->id,
            'subject_id' => $this->subject->id,
            'level' => 'N4',
            'exam_type' => 'jlpt_simulation',
            'duration_minutes' => 60,
            'passing_score' => 90,
            'max_score' => 180,
            'is_published' => true,
            'display_mode' => 'formal',
            'allow_student_mode_switch' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('exams', [
            'code' => 'EXAM-CBT-12',
            'question_bank_id' => $bank->id,
        ]);

        $exam = Exam::where('code', 'EXAM-CBT-12')->first();
        $this->assertEquals(1, $exam->questions()->count());

        // 4. Update the Exam in /sensei/exams (Edit button test)
        $updateResponse = $this->actingAs($this->sensei)->put(route('sensei.exams.update', $exam->id), [
            'title' => 'Simulasi CBT N4 Angkatan 12 - Revisi Jadwal',
            'code' => 'EXAM-CBT-12',
            'question_bank_id' => $bank->id,
            'subject_id' => $this->subject->id,
            'level' => 'N4',
            'exam_type' => 'jlpt_simulation',
            'duration_minutes' => 90,
            'passing_score' => 100,
            'max_score' => 180,
            'is_published' => true,
            'display_mode' => 'game',
            'allow_student_mode_switch' => true,
        ]);

        $updateResponse->assertRedirect();
        $exam->refresh();
        $this->assertEquals('Simulasi CBT N4 Angkatan 12 - Revisi Jadwal', $exam->title);
        $this->assertEquals(90, $exam->duration_minutes);
        $this->assertEquals('game', $exam->display_mode);

        // 5. CRITICAL TEST: Delete Exam in /sensei/exams
        $deleteResponse = $this->actingAs($this->sensei)->delete(route('sensei.exams.destroy', $exam->id));
        $deleteResponse->assertRedirect();

        // The Exam record must be deleted
        $this->assertDatabaseMissing('exams', [
            'id' => $exam->id,
        ]);

        // BUT the Master Question Bank and Question MUST REMAIN 100% INTACT!
        $this->assertDatabaseHas('question_banks', [
            'id' => $bank->id,
            'title' => 'Master Bank Soal JLPT N4 Paket A',
        ]);
        $this->assertDatabaseHas('questions', [
            'id' => $q1->id,
            'question_text' => 'これは本です。',
        ]);
        $this->assertEquals(1, $bank->fresh()->questions()->count());
    }
}
