<?php

namespace Tests\Feature;

use App\Models\QuestionCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LearningIndicatorCrudTest extends TestCase
{
    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        // Create or get admin role
        Role::firstOrCreate(['name' => 'admin']);

        // Create admin user
        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_view_learning_indicators_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.master.learning-indicators.index'));
        $response->assertStatus(200);
    }

    public function test_admin_can_create_learning_indicator_with_only_name_and_description(): void
    {
        $payload = [
            'name' => 'Indikator Uji Kompetensi Kosakata ' . uniqid(),
            'description' => 'Penjelasan standar capaian kompetensi dasar.',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.master.learning-indicators.store'), $payload);
        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('question_categories', [
            'name' => $payload['name'],
            'description' => $payload['description'],
        ]);
    }

    public function test_admin_can_update_learning_indicator(): void
    {
        $indicator = QuestionCategory::create([
            'name' => 'Indikator Lama ' . uniqid(),
            'description' => 'Deskripsi lama',
            'section_type' => 'bunpou',
            'level' => 'N4',
        ]);

        $payload = [
            'name' => 'Indikator Baru Diperbarui ' . uniqid(),
            'description' => 'Deskripsi yang diperbarui.',
        ];

        $response = $this->actingAs($this->admin)
            ->put(route('admin.master.learning-indicators.update', $indicator->id), $payload);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('question_categories', [
            'id' => $indicator->id,
            'name' => $payload['name'],
            'description' => $payload['description'],
        ]);
    }

    public function test_admin_can_delete_unused_learning_indicator(): void
    {
        $indicator = QuestionCategory::create([
            'name' => 'Indikator Dihapus ' . uniqid(),
            'section_type' => 'bunpou',
            'level' => 'N4',
        ]);

        $response = $this->actingAs($this->admin)
            ->delete(route('admin.master.learning-indicators.destroy', $indicator->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('question_categories', [
            'id' => $indicator->id,
        ]);
    }
}
