<?php

namespace Tests\Feature;

use App\Models\Chapter;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SenseiLmsCurriculumTableTest extends TestCase
{
    use RefreshDatabase;

    protected User $sensei;
    protected Course $course;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'sensei']);
        $this->sensei = User::factory()->create();
        $this->sensei->assignRole('sensei');

        $this->course = Course::create([
            'title' => 'Minna no Nihongo I',
            'slug' => 'minna-no-nihongo-1',
            'level' => 'N5',
        ]);

        for ($i = 1; $i <= 25; $i++) {
            Chapter::create([
                'course_id' => $this->course->id,
                'chapter_number' => $i,
                'title' => "Bab $i: Materi Pembelajaran",
                'description' => "Tata bahasa dan percakapan Bab $i",
                'is_published' => $i <= 20,
            ]);
        }
    }

    public function test_sensei_can_view_lms_index_with_table_data()
    {
        $response = $this->actingAs($this->sensei)->get(route('sensei.lms.index', [
            'course_id' => $this->course->id,
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Sensei/Lms/Index')
            ->has('chapters.data', 20) // default 20 per page
            ->where('chapters.total', 25)
            ->where('filters.per_page', 20)
        );
    }

    public function test_sensei_can_filter_lms_table_by_search_and_status()
    {
        $response = $this->actingAs($this->sensei)->get(route('sensei.lms.index', [
            'course_id' => $this->course->id,
            'search' => 'Bab 5',
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Sensei/Lms/Index')
            ->has('chapters.data')
            ->where('chapters.total', 1)
        );

        // Filter status draft
        $draftResponse = $this->actingAs($this->sensei)->get(route('sensei.lms.index', [
            'course_id' => $this->course->id,
            'status' => 'draft',
        ]));

        $draftResponse->assertOk();
        $draftResponse->assertInertia(fn ($page) => $page
            ->component('Sensei/Lms/Index')
            ->where('chapters.total', 5) // 21-25 are draft
        );
    }

    public function test_sensei_can_change_per_page_pagination()
    {
        $response = $this->actingAs($this->sensei)->get(route('sensei.lms.index', [
            'course_id' => $this->course->id,
            'per_page' => 10,
        ]));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Sensei/Lms/Index')
            ->has('chapters.data', 10)
            ->where('filters.per_page', 10)
        );
    }
}
