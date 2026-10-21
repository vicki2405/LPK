<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SenseiVocabularyCrudTest extends TestCase
{
    use RefreshDatabase;

    protected User $sensei;

    protected function setUp(): void
    {
        parent::setUp();
        Role::firstOrCreate(['name' => 'sensei']);
        $this->sensei = User::factory()->create();
        $this->sensei->assignRole('sensei');
    }

    public function test_sensei_can_create_vocabulary_standalone_without_level_and_chapter()
    {
        $response = $this->actingAs($this->sensei)->post(route('sensei.vocabularies.store'), [
            'category' => 'Pekerjaan & Kantor',
            'word_type' => 'Kata Benda',
            'kanji' => '会社',
            'hiragana' => 'かいしゃ',
            'romaji' => 'kaisha',
            'meaning_id' => 'Perusahaan / Kantor',
            'example_sentence_jp' => '日本の会社で働きます。',
            'example_sentence_id' => 'Saya bekerja di perusahaan Jepang.',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('vocabularies', [
            'kanji' => '会社',
            'hiragana' => 'かいしゃ',
            'meaning_id' => 'Perusahaan / Kantor',
            'category' => 'Pekerjaan & Kantor',
            'word_type' => 'Kata Benda',
            'chapter_id' => null,
            'level' => 'N5',
        ]);
    }

    public function test_sensei_can_update_vocabulary_standalone()
    {
        $vocab = Vocabulary::create([
            'kanji' => '朝ご飯',
            'hiragana' => 'あさごはん',
            'meaning_id' => 'Sarapan pagi',
            'category' => 'Makanan',
            'word_type' => 'Kata Benda',
            'level' => 'N5',
            'chapter_id' => null,
        ]);

        $response = $this->actingAs($this->sensei)->put(route('sensei.vocabularies.update', $vocab->id), [
            'category' => 'Makanan & Minuman',
            'word_type' => 'Kata Benda Makanan',
            'kanji' => '朝ごはん',
            'hiragana' => 'あさごはん',
            'meaning_id' => 'Makan Pagi / Sarapan',
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('vocabularies', [
            'id' => $vocab->id,
            'kanji' => '朝ごはん',
            'meaning_id' => 'Makan Pagi / Sarapan',
            'word_type' => 'Kata Benda Makanan',
            'category' => 'Makanan & Minuman',
        ]);
    }

    public function test_sensei_can_delete_vocabulary()
    {
        $vocab = Vocabulary::create([
            'hiragana' => 'ほん',
            'meaning_id' => 'Buku',
            'category' => 'Benda',
            'word_type' => 'Kata Benda',
            'level' => 'N5',
            'chapter_id' => null,
        ]);

        $response = $this->actingAs($this->sensei)->delete(route('sensei.vocabularies.destroy', $vocab->id));

        $response->assertRedirect();
        $this->assertDatabaseMissing('vocabularies', ['id' => $vocab->id]);
    }

    public function test_sensei_can_auto_translate_indonesian_meaning_to_japanese()
    {
        $response = $this->actingAs($this->sensei)->postJson(route('sensei.vocabularies.auto-translate'), [
            'meaning' => 'guru',
        ]);

        $response->assertOk();
        $response->assertJson([
            'found' => true,
            'kanji' => '先生',
            'hiragana' => 'せんせい',
            'romaji' => 'sensei',
        ]);
    }

    public function test_sensei_can_create_vocabulary_with_explicit_level_and_chapter()
    {
        $course = \App\Models\Course::create([
            'title' => 'Bahasa Jepang N4',
            'slug' => 'bahasa-jepang-n4-test',
            'level' => 'N4',
        ]);
        $chapter = \App\Models\Chapter::create([
            'course_id' => $course->id,
            'chapter_number' => 26,
            'title' => 'Bab 26',
        ]);

        $response = $this->actingAs($this->sensei)->post(route('sensei.vocabularies.store'), [
            'level' => 'N4',
            'chapter_id' => $chapter->id,
            'category' => 'Medis & Kaigo',
            'word_type' => 'Kata Benda',
            'kanji' => '病院',
            'hiragana' => 'びょういん',
            'romaji' => 'byouin',
            'meaning_id' => 'Rumah Sakit',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('vocabularies', [
            'level' => 'N4',
            'chapter_id' => $chapter->id,
            'category' => 'Medis & Kaigo',
            'kanji' => '病院',
            'hiragana' => 'びょういん',
            'meaning_id' => 'Rumah Sakit',
        ]);
    }

    public function test_sensei_can_stream_native_japanese_pronunciation_audio()
    {
        \Illuminate\Support\Facades\Http::fake([
            'translate.google.com/*' => \Illuminate\Support\Facades\Http::response(str_repeat('FAKE_AUDIO_DATA_FOR_TEST', 20), 200, ['Content-Type' => 'audio/mpeg']),
        ]);

        $response = $this->actingAs($this->sensei)->get(route('sensei.vocabularies.pronunciation-audio', [
            'text' => 'せんせい',
        ]));

        $response->assertOk();
        $this->assertEquals('audio/mpeg', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($response->getContent());
    }

    public function test_sensei_can_generate_and_save_native_japanese_audio()
    {
        \Illuminate\Support\Facades\Http::fake([
            'translate.google.com/*' => \Illuminate\Support\Facades\Http::response(str_repeat('FAKE_AUDIO_DATA_FOR_TEST', 20), 200, ['Content-Type' => 'audio/mpeg']),
        ]);

        $response = $this->actingAs($this->sensei)->postJson(route('sensei.vocabularies.generate-native-audio'), [
            'text' => 'せんせい',
        ]);

        $response->assertOk();
        $response->assertJsonStructure(['audio_url']);
        $this->assertStringContainsString('/storage/lms/vocab_audios/', $response->json('audio_url'));
    }

    public function test_siswa_can_stream_native_japanese_pronunciation_audio_for_flashcards()
    {
        \Illuminate\Support\Facades\Http::fake([
            'translate.google.com/*' => \Illuminate\Support\Facades\Http::response(str_repeat('FAKE_AUDIO_DATA_FOR_TEST', 20), 200, ['Content-Type' => 'audio/mpeg']),
        ]);

        Role::firstOrCreate(['name' => 'siswa']);
        $siswa = User::factory()->create();
        $siswa->assignRole('siswa');

        $response = $this->actingAs($siswa)->get(route('siswa.flashcards.pronunciation-audio', [
            'text' => 'ありがとうございます',
        ]));

        $response->assertOk();
        $this->assertEquals('audio/mpeg', $response->headers->get('Content-Type'));
        $this->assertNotEmpty($response->getContent());
    }
}

