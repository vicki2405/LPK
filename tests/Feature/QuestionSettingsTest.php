<?php

namespace Tests\Feature;

use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class QuestionSettingsTest extends TestCase
{
    use RefreshDatabase;

    protected User $sensei;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'sensei']);
        Role::firstOrCreate(['name' => 'admin']);

        $this->sensei = User::factory()->create();
        $this->sensei->assignRole('sensei');
    }

    public function test_sensei_can_view_question_settings_index_with_counts(): void
    {
        $level = QuestionLevel::where('code', 'N5')->firstOrFail();

        $category = QuestionCategory::create([
            'name' => 'Moji-Goi Test',
            'code' => 'MGT',
            'section_type' => 'moji_goi',
            'level_code' => 'N5',
            'level' => 'N5',
        ]);

        Question::create([
            'created_by' => $this->sensei->id,
            'question_category_id' => $category->id,
            'level' => 'N5',
            'level_code' => 'N5',
            'section_type' => 'moji_goi',
            'question_text' => 'Sample test question',
        ]);

        $response = $this->actingAs($this->sensei)
            ->get(route('sensei.settings.questions'));

        $response->assertOk();
        $response->assertInertia(fn ($page) => $page
            ->component('Sensei/Settings/QuestionSettings')
            ->has('levels')
            ->has('categories')
        );
    }

    public function test_sensei_can_create_level(): void
    {
        $response = $this->actingAs($this->sensei)
            ->post(route('sensei.settings.levels.store'), [
                'code' => 'NAT_Q4',
                'name' => 'NAT-TEST Q4',
                'description' => 'Tingkat ujian NAT',
                'order_index' => 10,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('question_levels', [
            'code' => 'NAT_Q4',
            'name' => 'NAT-TEST Q4',
        ]);
    }

    public function test_sensei_can_create_level_without_code_input(): void
    {
        $response = $this->actingAs($this->sensei)
            ->post(route('sensei.settings.levels.store'), [
                'name' => 'Pra Pemberangkatan',
                'description' => 'Tingkat persiapan kerja',
                'order_index' => 5,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('question_levels', [
            'code' => 'PRA_PEMBERANGKAT',
            'name' => 'Pra Pemberangkatan',
        ]);
    }

    public function test_sensei_cannot_delete_level_in_use_by_questions(): void
    {
        $level = QuestionLevel::where('code', 'N4')->firstOrFail();

        Question::create([
            'created_by' => $this->sensei->id,
            'level' => 'N4',
            'level_code' => 'N4',
            'section_type' => 'bunpou',
            'question_text' => 'N4 Question',
        ]);

        $response = $this->actingAs($this->sensei)
            ->delete(route('sensei.settings.levels.destroy', $level->id));

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseHas('question_levels', ['id' => $level->id]);
    }

    public function test_sensei_can_delete_unused_level(): void
    {
        $level = QuestionLevel::create([
            'code' => 'LV_UNUSED',
            'name' => 'Unused Level',
            'order_index' => 99,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->sensei)
            ->delete(route('sensei.settings.levels.destroy', $level->id));

        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('question_levels', ['id' => $level->id]);
    }

    public function test_sensei_can_create_category_with_level_code(): void
    {
        $response = $this->actingAs($this->sensei)
            ->post(route('sensei.settings.categories.store'), [
                'name' => 'Pemahaman Wacana',
                'code' => 'DOK-01',
                'section_type' => 'dokkai',
                'level_code' => 'N4',
                'description' => 'Membaca artikel',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('question_categories', [
            'name' => 'Pemahaman Wacana',
            'code' => 'DOK-01',
            'section_type' => 'dokkai',
            'level_code' => 'N4',
        ]);
    }

    public function test_sensei_can_create_category_without_code_input(): void
    {
        $response = $this->actingAs($this->sensei)
            ->post(route('sensei.settings.categories.store'), [
                'name' => 'Kategori Tanpa Kode',
                'section_type' => 'choukai',
                'level_code' => 'N5',
                'description' => 'Mendengarkan percakapan',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('question_categories', [
            'name' => 'Kategori Tanpa Kode',
            'section_type' => 'choukai',
            'level_code' => 'N5',
            'code' => null,
        ]);
    }

    public function test_sensei_can_delete_category_with_force_detach(): void
    {
        $cat = QuestionCategory::create([
            'name' => 'Kategori Uji',
            'code' => 'TEST',
            'section_type' => 'bunpou',
            'level_code' => 'N4',
            'level' => 'N4',
        ]);

        $q = Question::create([
            'created_by' => $this->sensei->id,
            'question_category_id' => $cat->id,
            'level' => 'N4',
            'level_code' => 'N4',
            'section_type' => 'bunpou',
            'question_text' => 'Pertanyaan uji',
        ]);

        // Standard delete fails without force_detach
        $resFail = $this->actingAs($this->sensei)
            ->delete(route('sensei.settings.categories.destroy', $cat->id));
        $resFail->assertSessionHasErrors('error');
        $this->assertDatabaseHas('question_categories', ['id' => $cat->id]);

        // Delete with force_detach succeeds and sets question_category_id to null
        $resSuccess = $this->actingAs($this->sensei)
            ->delete(route('sensei.settings.categories.destroy', $cat->id), ['force_detach' => true]);
        $resSuccess->assertSessionHas('success');
        $this->assertDatabaseMissing('question_categories', ['id' => $cat->id]);

        $this->assertDatabaseHas('questions', [
            'id' => $q->id,
            'question_category_id' => null,
        ]);
    }
}
