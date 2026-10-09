<?php

namespace Tests\Feature;

use App\Models\Kanji;
use App\Models\LanguageLevel;
use App\Models\Topic;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SenseiTopicManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $sensei;
    protected User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'sensei', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);

        $this->sensei = User::factory()->create();
        $this->sensei->assignRole('sensei');

        $this->siswa = User::factory()->create();
        $this->siswa->assignRole('siswa');
    }

    public function test_sensei_can_create_vocabulary_topic(): void
    {
        $response = $this->actingAs($this->sensei)->post(route('sensei.vocabularies.topics.store'), [
            'level' => 'N5',
            'title' => 'Kehidupan Sehari-hari',
            'description' => 'Kosakata seputar kegiatan rumah dan sekolah',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('topics', [
            'type' => 'vocabulary',
            'level' => 'N5',
            'title' => 'Kehidupan Sehari-hari',
        ]);
    }

    public function test_sensei_can_update_vocabulary_topic(): void
    {
        $topic = Topic::create([
            'type' => 'vocabulary',
            'level' => 'N5',
            'title' => 'Judul Lama',
            'description' => 'Deskripsi lama',
            'created_by' => $this->sensei->id,
        ]);

        $response = $this->actingAs($this->sensei)->put(route('sensei.vocabularies.topics.update', $topic->id), [
            'level' => 'N5',
            'title' => 'Judul Baru Diperbarui',
            'description' => 'Deskripsi baru',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('topics', [
            'id' => $topic->id,
            'title' => 'Judul Baru Diperbarui',
        ]);
    }

    public function test_sensei_can_delete_vocabulary_topic(): void
    {
        $topic = Topic::create([
            'type' => 'vocabulary',
            'level' => 'N5',
            'title' => 'Topik Dihapus',
            'created_by' => $this->sensei->id,
        ]);

        $response = $this->actingAs($this->sensei)->delete(route('sensei.vocabularies.topics.destroy', $topic->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('topics', [
            'id' => $topic->id,
        ]);
    }

    public function test_sensei_can_create_kanji_topic(): void
    {
        $response = $this->actingAs($this->sensei)->post(route('sensei.kanjis.topics.store'), [
            'level' => 'N5',
            'title' => 'Angka & Bilangan',
            'description' => 'Kanji satu sampai sepuluh',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('topics', [
            'type' => 'kanji',
            'level' => 'N5',
            'title' => 'Angka & Bilangan',
        ]);
    }

    public function test_vocabulary_can_be_associated_with_topic(): void
    {
        $topic = Topic::create([
            'type' => 'vocabulary',
            'level' => 'N5',
            'title' => 'Kata Kerja Minna',
            'created_by' => $this->sensei->id,
        ]);

        $response = $this->actingAs($this->sensei)->post(route('sensei.vocabularies.store'), [
            'level' => 'N5',
            'topic_ids' => [$topic->id],
            'kanji' => '食べる',
            'hiragana' => 'たべる',
            'romaji' => 'taberu',
            'meaning_id' => 'Makan',
            'word_type' => 'Kata Kerja Golongan II',
        ]);

        $response->assertRedirect(route('sensei.vocabularies.index'));
        $this->assertDatabaseHas('vocabularies', [
            'hiragana' => 'たべる',
            'meaning_id' => 'Makan',
        ]);

        $vocab = Vocabulary::where('hiragana', 'たべる')->first();
        $this->assertNotNull($vocab);
        $this->assertTrue($vocab->topics->contains($topic->id));
    }

    public function test_siswa_can_view_flashcards_filtered_by_topic(): void
    {
        $topic = Topic::create([
            'type' => 'vocabulary',
            'level' => 'N5',
            'title' => 'Topik Siswa',
            'created_by' => $this->sensei->id,
        ]);

        $vocab = Vocabulary::create([
            'level' => 'N5',
            'category' => 'Topik Siswa',
            'hiragana' => 'ねこ',
            'meaning_id' => 'Kucing',
            'word_type' => 'Kata Benda',
        ]);
        $vocab->topics()->attach($topic->id);

        $response = $this->actingAs($this->siswa)->get(route('siswa.flashcards.index', [
            'level' => 'N5',
            'topic_id' => $topic->id,
        ]));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Siswa/Flashcards/Index')
            ->has('topics')
            ->where('selectedTopicId', $topic->id)
        );
    }
}
