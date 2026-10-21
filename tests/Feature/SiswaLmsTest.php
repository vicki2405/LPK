<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\QuestionCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SiswaLmsTest extends TestCase
{
    use RefreshDatabase;

    protected User $siswa;
    protected Course $course;
    protected Chapter $chapter;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'siswa']);

        $this->siswa = User::factory()->create();
        $this->siswa->assignRole('siswa');

        $this->course = Course::create([
            'title' => 'Bahasa Jepang N5',
            'slug' => 'bahasa-jepang-n5',
            'level' => 'N5',
            'is_published' => true,
        ]);

        $this->chapter = Chapter::create([
            'course_id' => $this->course->id,
            'chapter_number' => 2,
            'title' => 'Bab 2: Benda & Penunjuk Ruang',
            'description' => 'Ini adalah materi bab 2 tentang kore, sore, are.',
            'is_published' => true,
        ]);
    }

    public function test_siswa_can_view_lms_index(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('siswa.lms.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Siswa/Lms/Index')
            ->has('courses', 1)
        );
    }

    public function test_siswa_can_view_lms_show_reader(): void
    {
        $indicator = QuestionCategory::create([
            'name' => 'Penunjuk Objek & Lokasi',
            'code' => 'IND-N5-02',
            'level' => 'N5',
            'chapter_id' => $this->chapter->id,
        ]);

        Lesson::create([
            'chapter_id' => $this->chapter->id,
            'title' => 'Pengenalan Kore, Sore, Are',
            'content_type' => 'text_grammar',
            'content_body' => 'Penjelasan mengenai kore, sore, are...',
            'duration_minutes' => 15,
            'is_published' => true,
        ]);

        $response = $this->actingAs($this->siswa)->get(route('siswa.lms.show', $this->chapter->id));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Siswa/Lms/Show')
            ->where('chapter.id', $this->chapter->id)
            ->where('chapter.title', 'Bab 2: Benda & Penunjuk Ruang')
            ->has('chapter.learning_indicators', 1)
            ->has('chapter.lessons', 1)
        );
    }
}
