<?php

namespace Tests\Feature;

use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionOption;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SiswaRaporTest extends TestCase
{
    use RefreshDatabase;

    protected User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'siswa']);

        $this->siswa = User::factory()->create([
            'name' => 'Kenji Tanaka',
            'target_language_level' => 'N4',
        ]);
        $this->siswa->assignRole('siswa');
    }

    public function test_guest_cannot_access_rapor(): void
    {
        $response = $this->get(route('siswa.rapor.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_siswa_can_view_rapor_index_empty_state(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('siswa.rapor.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Siswa/Rapor/Index')
            ->has('student')
            ->where('student.name', 'Kenji Tanaka')
        );
    }

    public function test_journey_route_redirects_to_rapor_index(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('siswa.journey.index'));

        $response->assertRedirect(route('siswa.rapor.index'));
    }

    public function test_siswa_can_view_rapor_with_cbt_session_and_indicators(): void
    {
        $category = QuestionCategory::create([
            'name' => 'Moji-Goi (Kosakata)',
            'code' => 'MG-N4',
            'level' => 'N4',
            'section_type' => 'moji_goi',
        ]);

        $exam = Exam::create([
            'title' => 'Simulasi JFT-Basic A2 Paket 1',
            'level' => 'N4',
            'duration_minutes' => 60,
            'passing_score' => 120,
            'max_score' => 180,
            'is_published' => true,
        ]);

        $question = Question::create([
            'question_category_id' => $category->id,
            'level' => 'N4',
            'section_type' => 'moji_goi',
            'question_text' => 'これはなんですか？',
            'score_points' => 5,
        ]);

        $optionCorrect = QuestionOption::create([
            'question_id' => $question->id,
            'option_key' => 'A',
            'option_text' => 'ほん',
            'is_correct' => true,
        ]);

        $session = ExamSession::create([
            'exam_id' => $exam->id,
            'user_id' => $this->siswa->id,
            'attempt_number' => 1,
            'started_at' => now()->subHour(),
            'expires_at' => now(),
            'submitted_at' => now()->subMinutes(10),
            'total_score' => 140,
            'is_passed' => true,
            'status' => 'submitted',
        ]);

        ExamAnswer::create([
            'exam_session_id' => $session->id,
            'question_id' => $question->id,
            'question_option_id' => $optionCorrect->id,
            'is_correct' => true,
            'score_earned' => 5,
        ]);

        $response = $this->actingAs($this->siswa)->get(route('siswa.rapor.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Siswa/Rapor/Index')
            ->has('student.exam_sessions', 1)
            ->where('student.exam_sessions.0.total_score', '140.00')
            ->where('student.exam_sessions.0.is_passed', true)
        );
    }
}
