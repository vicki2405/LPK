<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CbtSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $sensei;
    protected User $student;
    protected User $otherStudent;
    protected Exam $exam;
    protected Question $question;
    protected QuestionOption $correctOption;
    protected QuestionOption $wrongOption;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'sensei']);
        Role::firstOrCreate(['name' => 'siswa']);

        $this->sensei = User::factory()->create();
        $this->sensei->assignRole('sensei');

        $this->student = User::factory()->create();
        $this->student->assignRole('siswa');

        $this->otherStudent = User::factory()->create();
        $this->otherStudent->assignRole('siswa');

        $subject = Subject::create([
            'name' => 'Bahasa Jepang N4',
            'code' => 'JPN-N4',
            'created_by' => $this->sensei->id,
        ]);

        $this->exam = Exam::create([
            'subject_id' => $subject->id,
            'creator_id' => $this->sensei->id,
            'title' => 'Ujian Resmi CBT N4',
            'code' => 'EX-N4-01',
            'level' => 'N4',
            'duration_minutes' => 60,
            'passing_score' => 90,
            'max_score' => 180,
            'display_mode' => 'formal',
            'is_published' => true,
            'allow_student_mode_switch' => true,
        ]);

        $this->question = Question::create([
            'created_by' => $this->sensei->id,
            'level' => 'N4',
            'section_type' => 'bunpou',
            'question_text' => 'これは本です。',
            'score_points' => 2.0,
        ]);

        $this->correctOption = QuestionOption::create([
            'question_id' => $this->question->id,
            'option_key' => 'A',
            'option_text' => 'ほん',
            'is_correct' => true,
        ]);

        $this->wrongOption = QuestionOption::create([
            'question_id' => $this->question->id,
            'option_key' => 'B',
            'option_text' => 'ペン',
            'is_correct' => false,
        ]);

        $this->exam->questions()->attach($this->question->id, [
            'section_type' => 'bunpou',
            'order_index' => 1,
        ]);
    }

    public function test_answer_key_is_never_exposed_in_student_cbt_session_payload(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'started_at' => now(),
            'expires_at' => now()->addMinutes(60),
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($this->student)->get(route('siswa.cbt.show', $session->id));

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Siswa/Cbt/Show')
            ->has('questions', 1)
            ->where('questions.0.options.0.is_correct', null)
            ->where('questions.0.options.1.is_correct', null)
        );
    }

    public function test_student_cannot_save_answer_after_exam_session_expires(): void
    {
        // Sesi yang sudah kedaluwarsa 10 menit lalu
        $expiredSession = ExamSession::create([
            'user_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'started_at' => now()->subMinutes(70),
            'expires_at' => now()->subMinutes(10),
            'status' => 'in_progress',
        ]);

        $response = $this->actingAs($this->student)->postJson(route('siswa.cbt.save-answer', $expiredSession->id), [
            'question_id' => $this->question->id,
            'question_option_id' => $this->correctOption->id,
        ]);

        $response->assertStatus(403);
        $response->assertJsonFragment(['error' => 'Waktu pengerjaan ujian telah berakhir.']);
    }

    public function test_student_cannot_access_or_modify_other_student_exam_session(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'started_at' => now(),
            'expires_at' => now()->addMinutes(60),
            'status' => 'in_progress',
        ]);

        // otherStudent mencoba mengakses sesi milik student
        $response = $this->actingAs($this->otherStudent)->get(route('siswa.cbt.show', $session->id));
        $response->assertStatus(403);

        $saveResponse = $this->actingAs($this->otherStudent)->postJson(route('siswa.cbt.save-answer', $session->id), [
            'question_id' => $this->question->id,
            'question_option_id' => $this->correctOption->id,
        ]);
        $saveResponse->assertStatus(403);
    }

    public function test_game_mode_returns_instant_feedback_without_prior_key_leak(): void
    {
        $session = ExamSession::create([
            'user_id' => $this->student->id,
            'exam_id' => $this->exam->id,
            'started_at' => now(),
            'expires_at' => now()->addMinutes(60),
            'status' => 'in_progress',
        ]);

        // Student mengirim jawaban di mode game
        $response = $this->actingAs($this->student)->postJson(route('siswa.cbt.save-answer', $session->id), [
            'question_id' => $this->question->id,
            'question_option_id' => $this->correctOption->id,
            'mode' => 'game',
        ]);

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'is_correct' => true,
        ]);
    }
}
