<?php

namespace App\Http\Controllers\Sensei;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\LanguageLevel;
use App\Models\Topic;
use App\Models\Vocabulary;
use App\Services\JapaneseDictionaryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class VocabularyController extends Controller
{
    /**
     * Display listing of all vocabularies (Kotoba Manager with Topic Grouping).
     */
    public function index(Request $request): Response
    {
        $query = Vocabulary::with(['chapter.course', 'topics'])->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('hiragana', 'like', "%{$search}%")
                  ->orWhere('kanji', 'like', "%{$search}%")
                  ->orWhere('romaji', 'like', "%{$search}%")
                  ->orWhere('meaning_id', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('example_sentence_jp', 'like', "%{$search}%")
                  ->orWhere('example_sentence_id', 'like', "%{$search}%");
            });
        }

        if ($request->filled('level') && $request->level !== 'all') {
            $query->where('level', $request->level);
        }

        if ($request->filled('topic_id')) {
            $topicId = $request->topic_id;
            $query->whereHas('topics', function ($q) use ($topicId) {
                $q->where('topics.id', $topicId);
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('chapter_id')) {
            if ($request->chapter_id === 'none') {
                $query->whereNull('chapter_id');
            } else {
                $query->where('chapter_id', $request->chapter_id);
            }
        }

        if ($request->filled('word_type')) {
            $query->where('word_type', $request->word_type);
        }

        if ($request->filled('has_audio')) {
            if ($request->has_audio === 'yes') {
                $query->whereNotNull('audio_file');
            } elseif ($request->has_audio === 'no') {
                $query->whereNull('audio_file');
            }
        }

        $vocabularies = $query->paginate(20)->withQueryString();

        $chapters = Chapter::with('course:id,title,level')
            ->orderBy('chapter_number', 'asc')
            ->get(['id', 'chapter_number', 'title', 'course_id']);

        $courses = Course::orderBy('id', 'asc')->get(['id', 'title', 'level']);

        $levels = $this->getLevels();

        // Ambil daftar Topik Kosakata
        $topics = Topic::forVocabulary()
            ->withCount('vocabularies')
            ->with(['vocabularies' => function ($q) {
                $q->select('vocabularies.id', 'vocabularies.kanji', 'vocabularies.hiragana', 'vocabularies.romaji', 'vocabularies.meaning_id');
            }])
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $existingCategories = Vocabulary::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->toArray();

        $defaultCategories = [
            'Kehidupan Sehari-hari',
            'Pekerjaan & Pabrik',
            'Restoran & Makanan',
            'Medis & Rumah Sakit',
            'Keluarga & Hubungan',
            'Arah, Lokasi & Fasilitas',
            'Kata Kerja Dasar',
            'Kata Sifat & Perasaan',
            'Salam & Ungkapan Kerja (Aisatsu)',
        ];

        $categories = array_values(array_unique(array_merge($defaultCategories, $existingCategories)));

        $wordTypes = [
            'Kata Benda',
            'Kata Kerja Golongan I',
            'Kata Kerja Golongan II',
            'Kata Kerja Golongan III',
            'Kata Sifat-i',
            'Kata Sifat-na',
            'Kata Keterangan',
            'Kata Sambung',
            'Ungkapan / Salam',
        ];

        $stats = [
            'total_vocabularies' => Vocabulary::count(),
            'total_topics' => $topics->count(),
            'total_with_audio' => Vocabulary::whereNotNull('audio_file')->count(),
            'total_n5' => Vocabulary::where('level', 'N5')->count(),
            'total_n4' => Vocabulary::where('level', 'N4')->count(),
            'total_standalone' => Vocabulary::whereNull('chapter_id')->count(),
            'total_verbs' => Vocabulary::where('word_type', 'like', '%Kata Kerja%')->count(),
        ];

        return Inertia::render('Sensei/Vocabularies/Index', [
            'vocabularies' => $vocabularies,
            'topics' => $topics,
            'chapters' => $chapters,
            'courses' => $courses,
            'levels' => $levels,
            'categories' => $categories,
            'wordTypes' => $wordTypes,
            'filters' => $request->only(['search', 'level', 'topic_id', 'category', 'chapter_id', 'course_id', 'word_type', 'has_audio']),
            'stats' => $stats,
        ]);
    }

    /**
     * Store a newly created topic for vocabulary.
     */
    public function storeTopic(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'level' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'Nama topik wajib diisi.',
            'level.required' => 'Level bahasa wajib dipilih.',
        ]);

        $maxSort = Topic::forVocabulary()->where('level', $validated['level'])->max('sort_order') ?? 0;

        Topic::create([
            'type' => 'vocabulary',
            'level' => $validated['level'],
            'title' => trim($validated['title']),
            'description' => $validated['description'] ? trim($validated['description']) : null,
            'sort_order' => $maxSort + 1,
            'created_by' => $request->user()?->id,
        ]);

        return redirect()->back()->with('success', "Topik [{$validated['title']}] berhasil dibuat!");
    }

    /**
     * Update the specified topic.
     */
    public function updateTopic(Request $request, Topic $topic): RedirectResponse
    {
        $validated = $request->validate([
            'level' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'title.required' => 'Nama topik wajib diisi.',
            'level.required' => 'Level bahasa wajib dipilih.',
        ]);

        $topic->update([
            'level' => $validated['level'],
            'title' => trim($validated['title']),
            'description' => $validated['description'] ? trim($validated['description']) : null,
        ]);

        return redirect()->back()->with('success', "Topik [{$topic->title}] berhasil diperbarui!");
    }

    /**
     * Delete the specified topic.
     */
    public function destroyTopic(Topic $topic): RedirectResponse
    {
        $title = $topic->title;
        $topic->delete();

        return redirect()->back()->with('success', "Topik [{$title}] berhasil dihapus!");
    }

    /**
     * Show form for creating a new vocabulary.
     */
    public function create(Request $request): Response
    {
        $chapters = Chapter::with('course:id,title,level')
            ->orderBy('chapter_number', 'asc')
            ->get(['id', 'chapter_number', 'title', 'course_id']);

        $topics = Topic::forVocabulary()
            ->orderBy('sort_order', 'asc')
            ->orderBy('title', 'asc')
            ->get(['id', 'title', 'level']);

        $wordTypes = [
            'Kata Benda',
            'Kata Kerja Golongan I',
            'Kata Kerja Golongan II',
            'Kata Kerja Golongan III',
            'Kata Sifat-i',
            'Kata Sifat-na',
            'Kata Keterangan',
            'Kata Sambung',
            'Ungkapan / Salam',
        ];

        $levels = $this->getLevels();

        return Inertia::render('Sensei/Vocabularies/Create', [
            'chapters' => $chapters,
            'topics' => $topics,
            'defaultTopicId' => $request->query('topic_id'),
            'defaultLevel' => $request->query('level', 'N5'),
            'wordTypes' => $wordTypes,
            'levels' => $levels,
        ]);
    }

    /**
     * Auto translate Indonesian meaning to Japanese kanji, hiragana, romaji.
     */
    public function autoTranslate(Request $request, JapaneseDictionaryService $dictService): JsonResponse
    {
        $meaning = $request->input('meaning', '');
        if (empty($meaning)) {
            return response()->json(['found' => false, 'message' => 'Teks arti diperlukan.'], 400);
        }

        $result = $dictService->translateIndonesianToJapanese($meaning);
        return response()->json($result);
    }

    /**
     * Stream real Japanese native pronunciation audio via server proxy.
     */
    public function pronunciationAudio(Request $request, JapaneseDictionaryService $dictService)
    {
        $text = $request->input('text', '');
        if (empty($text)) {
            abort(400, 'Teks pelafalan diperlukan.');
        }

        $audioBinary = $dictService->getNativePronunciationAudio($text);
        if (!$audioBinary) {
            abort(404, 'Audio pelafalan tidak dapat diakses saat ini.');
        }

        return response($audioBinary, 200, [
            'Content-Type' => 'audio/mpeg',
            'Cache-Control' => 'public, max-age=604800',
            'Content-Disposition' => 'inline; filename="pronunciation.mp3"',
        ]);
    }

    /**
     * Generate & permanently save authentic Japanese pronunciation MP3 file into public storage.
     */
    public function generateNativeAudio(Request $request, JapaneseDictionaryService $dictService): JsonResponse
    {
        $text = $request->input('text', '');
        if (empty($text)) {
            return response()->json(['success' => false, 'message' => 'Teks pelafalan diperlukan.'], 400);
        }

        $audioUrl = $dictService->saveNativePronunciationFile($text);
        if (!$audioUrl) {
            return response()->json(['success' => false, 'message' => 'Gagal mengunduh audio pelafalan asli.'], 500);
        }

        return response()->json([
            'success' => true,
            'audio_url' => $audioUrl,
            'message' => 'Audio penutur asli Jepang berhasil disimpan ke server LPK!',
        ]);
    }

    /**
     * Store a newly created vocabulary in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'level' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'topic_ids' => 'nullable|array',
            'topic_ids.*' => 'exists:topics,id',
            'chapter_id' => 'nullable',
            'kanji' => 'nullable|string|max:100',
            'hiragana' => 'required|string|max:100',
            'romaji' => 'nullable|string|max:100',
            'meaning_id' => 'required|string|max:255',
            'word_type' => 'nullable|string|max:100',
            'image_file' => 'nullable|image|max:5120',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg,m4a|max:10240',
            'generated_audio_url' => 'nullable|string',
            'example_sentence_jp' => 'nullable|string',
            'example_sentence_id' => 'nullable|string',
        ]);

        $validated['chapter_id'] = ($request->filled('chapter_id') && $request->chapter_id !== 'none') ? $request->chapter_id : null;
        $validated['level'] = $request->input('level') ?: 'N5';
        $validated['word_type'] = $request->input('word_type') ?: 'Kata Benda';

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('lms/vocab_images', 'public');
            $validated['image_file'] = '/storage/' . $path;
        }

        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('lms/vocab_audios', 'public');
            $validated['audio_file'] = '/storage/' . $path;
        } elseif ($request->filled('generated_audio_url')) {
            $validated['audio_file'] = $request->input('generated_audio_url');
        }

        // Simpan topik primer sebagai kategori jika ada
        if (!empty($validated['topic_ids'])) {
            $firstTopic = Topic::find($validated['topic_ids'][0]);
            if ($firstTopic) {
                $validated['category'] = $firstTopic->title;
                $validated['level'] = $firstTopic->level;
            }
        }

        if (empty($validated['category'])) {
            $validated['category'] = 'Umum / Sehari-hari';
        }

        $vocab = Vocabulary::create($validated);

        if (!empty($validated['topic_ids'])) {
            $vocab->topics()->sync($validated['topic_ids']);
        }

        return redirect()->route('sensei.vocabularies.index')->with('success', "Kosakata [{$vocab->hiragana}] ({$vocab->meaning_id}) berhasil ditambahkan!");
    }

    /**
     * Show form for editing the specified vocabulary.
     */
    public function edit(Vocabulary $vocabulary): Response
    {
        $vocabulary->load('topics');

        $chapters = Chapter::with('course:id,title,level')
            ->orderBy('chapter_number', 'asc')
            ->get(['id', 'chapter_number', 'title', 'course_id']);

        $topics = Topic::forVocabulary()
            ->orderBy('sort_order', 'asc')
            ->orderBy('title', 'asc')
            ->get(['id', 'title', 'level']);

        $wordTypes = [
            'Kata Benda',
            'Kata Kerja Golongan I',
            'Kata Kerja Golongan II',
            'Kata Kerja Golongan III',
            'Kata Sifat-i',
            'Kata Sifat-na',
            'Kata Keterangan',
            'Kata Sambung',
            'Ungkapan / Salam',
        ];

        $levels = $this->getLevels();

        return Inertia::render('Sensei/Vocabularies/Edit', [
            'vocabulary' => $vocabulary,
            'topics' => $topics,
            'selectedTopicIds' => $vocabulary->topics->pluck('id')->toArray(),
            'wordTypes' => $wordTypes,
            'chapters' => $chapters,
            'levels' => $levels,
        ]);
    }

    /**
     * Update the specified vocabulary in storage.
     */
    public function update(Request $request, Vocabulary $vocabulary): RedirectResponse
    {
        $validated = $request->validate([
            'level' => 'nullable|string|max:50',
            'category' => 'nullable|string|max:100',
            'topic_ids' => 'nullable|array',
            'topic_ids.*' => 'exists:topics,id',
            'chapter_id' => 'nullable',
            'kanji' => 'nullable|string|max:100',
            'hiragana' => 'required|string|max:100',
            'romaji' => 'nullable|string|max:100',
            'meaning_id' => 'required|string|max:255',
            'word_type' => 'nullable|string|max:100',
            'image_file' => 'nullable',
            'audio_file' => 'nullable',
            'generated_audio_url' => 'nullable|string',
            'example_sentence_jp' => 'nullable|string',
            'example_sentence_id' => 'nullable|string',
        ]);

        $validated['chapter_id'] = ($request->filled('chapter_id') && $request->chapter_id !== 'none') ? $request->chapter_id : null;
        $validated['level'] = $request->input('level') ?: ($vocabulary->level ?: 'N5');
        $validated['word_type'] = $request->input('word_type') ?: ($vocabulary->word_type ?: 'Kata Benda');

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('lms/vocab_images', 'public');
            $validated['image_file'] = '/storage/' . $path;
        } else {
            unset($validated['image_file']);
        }

        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('lms/vocab_audios', 'public');
            $validated['audio_file'] = '/storage/' . $path;
        } elseif ($request->filled('generated_audio_url')) {
            $validated['audio_file'] = $request->input('generated_audio_url');
        } else {
            unset($validated['audio_file']);
        }

        if (!empty($validated['topic_ids'])) {
            $firstTopic = Topic::find($validated['topic_ids'][0]);
            if ($firstTopic) {
                $validated['category'] = $firstTopic->title;
                $validated['level'] = $firstTopic->level;
            }
            $vocabulary->topics()->sync($validated['topic_ids']);
        } else {
            $vocabulary->topics()->detach();
        }

        if (empty($validated['category'])) {
            $validated['category'] = 'Umum / Sehari-hari';
        }

        $vocabulary->update($validated);

        return redirect()->route('sensei.vocabularies.index')->with('success', "Kosakata [{$vocabulary->hiragana}] berhasil diperbarui!");
    }

    /**
     * Remove the specified vocabulary from storage.
     */
    public function destroy(Vocabulary $vocabulary): RedirectResponse
    {
        $hiragana = $vocabulary->hiragana;
        $vocabulary->topics()->detach();
        $vocabulary->delete();

        return redirect()->back()->with('success', "Kosakata [{$hiragana}] berhasil dihapus dari database!");
    }

    /**
     * Get active language levels from database with fallback.
     */
    private function getLevels(): array
    {
        $dbLevels = LanguageLevel::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        if ($dbLevels->isNotEmpty()) {
            $levels = [];
            foreach ($dbLevels as $lvl) {
                $levels[$lvl->code] = $lvl->name;
            }
            return $levels;
        }

        return [
            'N5' => 'JLPT N5 (Tingkat Dasar)',
            'N4' => 'JLPT N4 & JFT-Basic A2 (Standar Kerja)',
            'N3' => 'JLPT N3 (Tingkat Menengah)',
            'N2' => 'JLPT N2 (Tingkat Mahir / Karir)',
            'N1' => 'JLPT N1 (Tingkat Fasih / Native)',
            'JFT_A2' => 'JFT-Basic A2 (SSW / Tokutei Ginou)',
        ];
    }
}