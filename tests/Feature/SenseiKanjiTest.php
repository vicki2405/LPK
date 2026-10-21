<?php

namespace Tests\Feature;

use App\Models\Kanji;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SenseiKanjiTest extends TestCase
{
    use RefreshDatabase;

    protected User $sensei;
    protected User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'sensei']);
        Role::firstOrCreate(['name' => 'siswa']);

        $this->sensei = User::factory()->create();
        $this->sensei->assignRole('sensei');

        $this->siswa = User::factory()->create();
        $this->siswa->assignRole('siswa');
    }

    public function test_sensei_can_view_kanji_index_page(): void
    {
        Kanji::create([
            'kanji' => '日',
            'hiragana' => 'ひ / にち',
            'meaning_id' => 'Matahari / Hari',
            'level' => 'N5',
        ]);

        $response = $this->actingAs($this->sensei)->get(route('sensei.kanjis.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Sensei/Kanji/Index')
            ->has('kanjis.data', 1)
            ->has('stats')
        );
    }

    public function test_non_sensei_cannot_access_sensei_kanji_index(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('sensei.kanjis.index'));

        $response->assertStatus(403);
    }

    public function test_sensei_can_filter_kanji_by_search_and_level(): void
    {
        Kanji::create([
            'kanji' => '日',
            'hiragana' => 'ひ',
            'meaning_id' => 'Matahari',
            'level' => 'N5',
        ]);

        Kanji::create([
            'kanji' => '校',
            'hiragana' => 'こう',
            'meaning_id' => 'Sekolah',
            'level' => 'N4',
        ]);

        $response = $this->actingAs($this->sensei)->get(route('sensei.kanjis.index', [
            'search' => 'Matahari',
            'level' => 'N5',
        ]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Sensei/Kanji/Index')
            ->has('kanjis.data', 1)
            ->where('kanjis.data.0.kanji', '日')
        );
    }

    public function test_sensei_can_create_new_kanji(): void
    {
        $response = $this->actingAs($this->sensei)->post(route('sensei.kanjis.store'), [
            'kanji' => '本',
            'hiragana' => 'ほん',
            'meaning_id' => 'Buku / Asal',
            'level' => 'N5',
            'romaji' => 'hon',
            'stroke_count' => 5,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kanjis', [
            'kanji' => '本',
            'meaning_id' => 'Buku / Asal',
            'created_by' => $this->sensei->id,
        ]);
    }

    public function test_sensei_can_update_existing_kanji(): void
    {
        $kanji = Kanji::create([
            'kanji' => '学',
            'hiragana' => 'まなぶ',
            'meaning_id' => 'Belajar',
            'level' => 'N5',
        ]);

        $response = $this->actingAs($this->sensei)->put(route('sensei.kanjis.update', $kanji->id), [
            'kanji' => '学',
            'hiragana' => 'まな・ぶ / がく',
            'meaning_id' => 'Belajar / Studi',
            'level' => 'N5',
            'notes' => 'Contoh: 学生 (Gakusei)',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kanjis', [
            'id' => $kanji->id,
            'meaning_id' => 'Belajar / Studi',
            'notes' => 'Contoh: 学生 (Gakusei)',
        ]);
    }

    public function test_sensei_can_delete_kanji(): void
    {
        $kanji = Kanji::create([
            'kanji' => '休',
            'hiragana' => 'やすむ',
            'meaning_id' => 'Istirahat',
            'level' => 'N5',
        ]);

        $response = $this->actingAs($this->sensei)->delete(route('sensei.kanjis.destroy', $kanji->id));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('kanjis', [
            'id' => $kanji->id,
        ]);
    }

    public function test_sensei_can_view_create_kanji_page(): void
    {
        $response = $this->actingAs($this->sensei)->get(route('sensei.kanjis.create'));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Sensei/Kanji/Create')
            ->has('languageLevels')
        );
    }

    public function test_sensei_can_view_edit_kanji_page(): void
    {
        $kanji = Kanji::create([
            'kanji' => '木',
            'hiragana' => 'き',
            'meaning_id' => 'Pohon',
            'level' => 'N5',
        ]);

        $response = $this->actingAs($this->sensei)->get(route('sensei.kanjis.edit', $kanji->id));
        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Sensei/Kanji/Edit')
            ->has('kanji')
            ->has('languageLevels')
        );
    }
}
