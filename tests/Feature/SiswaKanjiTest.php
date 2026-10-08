<?php

namespace Tests\Feature;

use App\Models\Kanji;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SiswaKanjiTest extends TestCase
{
    use RefreshDatabase;

    protected User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'sensei']);
        Role::firstOrCreate(['name' => 'siswa']);

        $this->siswa = User::factory()->create();
        $this->siswa->assignRole('siswa');
    }

    public function test_siswa_can_view_kanji_learning_page(): void
    {
        Kanji::create([
            'kanji' => '日',
            'hiragana' => 'ひ / にち',
            'meaning_id' => 'Matahari / Hari',
            'level' => 'N5',
        ]);

        $response = $this->actingAs($this->siswa)->get(route('siswa.kanjis.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Siswa/Kanji/Index')
            ->has('kanjis', 1)
            ->where('kanjis.0.kanji', '日')
            ->has('stats')
        );
    }

    public function test_guest_cannot_access_siswa_kanji_page(): void
    {
        $response = $this->get(route('siswa.kanjis.index'));

        $response->assertRedirect(route('login'));
    }

    public function test_siswa_can_filter_kanji_by_search_and_level(): void
    {
        Kanji::create([
            'kanji' => '月',
            'hiragana' => 'つき',
            'meaning_id' => 'Bulan',
            'level' => 'N5',
        ]);

        Kanji::create([
            'kanji' => '校',
            'hiragana' => 'こう',
            'meaning_id' => 'Sekolah',
            'level' => 'N4',
        ]);

        $response = $this->actingAs($this->siswa)->get(route('siswa.kanjis.index', [
            'level' => 'N5',
        ]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Siswa/Kanji/Index')
            ->has('kanjis', 1)
            ->where('kanjis.0.kanji', '月')
        );
    }
}
