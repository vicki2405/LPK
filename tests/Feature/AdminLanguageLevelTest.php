<?php

namespace Tests\Feature;

use App\Models\LanguageLevel;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminLanguageLevelTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $sensei;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sensei', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->sensei = User::factory()->create();
        $this->sensei->assignRole('sensei');
    }

    public function test_admin_can_view_language_levels_index(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.master.language-levels.index'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Admin/Master/LanguageLevels/Index'));
    }

    public function test_non_admin_cannot_access_language_levels_index(): void
    {
        $response = $this->actingAs($this->sensei)->get(route('admin.master.language-levels.index'));
        $response->assertStatus(403);
    }

    public function test_admin_can_create_language_level_without_code(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.master.language-levels.store'), [
            'name' => 'JLPT N5 (Tingkat Dasar)',
            'name_jp' => '日本語能力試験 N5',
            'description' => 'Materi dasar kosakata & kanji.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('language_levels', [
            'code' => 'N5',
            'name' => 'JLPT N5 (Tingkat Dasar)',
        ]);
    }

    public function test_admin_can_create_language_level_with_custom_code(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.master.language-levels.store'), [
            'code' => 'SSW_KAIGO',
            'name' => 'SSW Kaigo (Perawat Lansia)',
            'name_jp' => '特定技能 介護分野',
            'description' => 'Standar bahasa Jepang keperawatan lansia.',
            'sort_order' => 7,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('language_levels', [
            'code' => 'SSW_KAIGO',
            'name' => 'SSW Kaigo (Perawat Lansia)',
        ]);
    }

    public function test_admin_can_update_language_level(): void
    {
        $level = LanguageLevel::firstOrCreate(
            ['code' => 'N5'],
            ['name' => 'JLPT N5 (Dasar)', 'sort_order' => 1, 'is_active' => true]
        );

        $response = $this->actingAs($this->admin)->put(route('admin.master.language-levels.update', $level->id), [
            'name' => 'JLPT N5 (Tingkat Dasar Diperbarui)',
            'name_jp' => 'JLPT N5 基礎',
            'description' => 'Materi N5 dasar.',
            'sort_order' => 1,
            'is_active' => true,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('language_levels', [
            'id' => $level->id,
            'code' => 'N5',
            'name' => 'JLPT N5 (Tingkat Dasar Diperbarui)',
        ]);
    }

    public function test_admin_can_delete_unused_language_level(): void
    {
        $level = LanguageLevel::create([
            'code' => 'TEMP_LVL',
            'name' => 'Temporary Level',
            'sort_order' => 99,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.master.language-levels.destroy', $level->id));
        $response->assertRedirect();
        $this->assertDatabaseMissing('language_levels', ['id' => $level->id]);
    }
}