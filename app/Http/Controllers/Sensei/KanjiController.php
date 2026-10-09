<?php

namespace App\Http\Controllers\Sensei;

use App\Http\Controllers\Controller;
use App\Models\Kanji;
use App\Models\LanguageLevel;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KanjiController extends Controller
{
    /**
     * Display listing of Kanjis with Topic Support.
     */
    public function index(Request $request): Response
    {
        $query = Kanji::query()->with('topics')->latest('id');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('kanji', 'like', "%{$search}%")
                  ->orWhere('hiragana', 'like', "%{$search}%")
                  ->orWhere('meaning_id', 'like', "%{$search}%")
                  ->orWhere('romaji', 'like', "%{$search}%")
                  ->orWhere('onyomi', 'like', "%{$search}%")
                  ->orWhere('kunyomi', 'like', "%{$search}%");
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

        $kanjis = $query->paginate(24)->withQueryString();

        $languageLevels = LanguageLevel::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        // Ambil daftar Topik Kanji
        $topics = Topic::forKanji()
            ->withCount('kanjis')
            ->with(['kanjis' => function ($q) {
                $q->select('kanjis.id', 'kanjis.kanji', 'kanjis.onyomi', 'kanjis.kunyomi', 'kanjis.meaning_id');
            }])
            ->orderBy('sort_order', 'asc')
            ->orderBy('id', 'asc')
            ->get();

        $stats = [
            'total' => Kanji::count(),
            'total_topics' => $topics->count(),
            'total_n5' => Kanji::where('level', 'N5')->count(),
            'total_n4' => Kanji::where('level', 'N4')->count(),
        ];

        return Inertia::render('Sensei/Kanji/Index', [
            'kanjis' => $kanjis,
            'topics' => $topics,
            'languageLevels' => $languageLevels,
            'filters' => [
                'search' => $request->search ?? '',
                'level' => $request->level ?? 'all',
                'topic_id' => $request->topic_id ?? null,
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Store a newly created topic for Kanji.
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

        $maxSort = Topic::forKanji()->where('level', $validated['level'])->max('sort_order') ?? 0;

        Topic::create([
            'type' => 'kanji',
            'level' => $validated['level'],
            'title' => trim($validated['title']),
            'description' => $validated['description'] ? trim($validated['description']) : null,
            'sort_order' => $maxSort + 1,
            'created_by' => $request->user()?->id,
        ]);

        return redirect()->back()->with('success', "Topik Kanji [{$validated['title']}] berhasil dibuat!");
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

        return redirect()->back()->with('success', "Topik Kanji [{$topic->title}] berhasil diperbarui!");
    }

    /**
     * Delete the specified topic.
     */
    public function destroyTopic(Topic $topic): RedirectResponse
    {
        $title = $topic->title;
        $topic->delete();

        return redirect()->back()->with('success', "Topik Kanji [{$title}] berhasil dihapus!");
    }

    /**
     * Show the form for creating a new Kanji.
     */
    public function create(Request $request): Response
    {
        $languageLevels = LanguageLevel::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        $topics = Topic::forKanji()
            ->orderBy('sort_order', 'asc')
            ->orderBy('title', 'asc')
            ->get(['id', 'title', 'level']);

        return Inertia::render('Sensei/Kanji/Create', [
            'languageLevels' => $languageLevels,
            'topics' => $topics,
            'defaultTopicId' => $request->query('topic_id'),
            'defaultLevel' => $request->query('level', 'N5'),
        ]);
    }

    /**
     * Store a newly created Kanji.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'kanji' => ['required', 'string', 'max:50'],
            'hiragana' => ['required', 'string', 'max:100'],
            'meaning_id' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:20'],
            'topic_ids' => ['nullable', 'array'],
            'topic_ids.*' => ['exists:topics,id'],
            'romaji' => ['nullable', 'string', 'max:100'],
            'onyomi' => ['nullable', 'string', 'max:255'],
            'kunyomi' => ['nullable', 'string', 'max:255'],
            'stroke_count' => ['nullable', 'integer', 'min:1', 'max:60'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'kanji.required' => 'Huruf Kanji wajib diisi.',
            'hiragana.required' => 'Cara baca Hiragana wajib diisi.',
            'meaning_id.required' => 'Arti bahasa Indonesia wajib diisi.',
            'level.required' => 'Level bahasa wajib dipilih.',
        ]);

        $validated['created_by'] = $request->user()?->id;

        $kanji = Kanji::create($validated);

        if (!empty($validated['topic_ids'])) {
            $kanji->topics()->sync($validated['topic_ids']);
        }

        return redirect()->route('sensei.kanjis.index')->with('success', "Huruf Kanji [{$validated['kanji']}] berhasil ditambahkan!");
    }

    /**
     * Show the form for editing the specified Kanji.
     */
    public function edit(Kanji $kanji): Response
    {
        $kanji->load('topics');
        $languageLevels = LanguageLevel::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        $topics = Topic::forKanji()
            ->orderBy('sort_order', 'asc')
            ->orderBy('title', 'asc')
            ->get(['id', 'title', 'level']);

        return Inertia::render('Sensei/Kanji/Edit', [
            'kanji' => $kanji,
            'topics' => $topics,
            'selectedTopicIds' => $kanji->topics->pluck('id')->toArray(),
            'languageLevels' => $languageLevels,
        ]);
    }

    /**
     * Update the specified Kanji.
     */
    public function update(Request $request, Kanji $kanji): RedirectResponse
    {
        $validated = $request->validate([
            'kanji' => ['required', 'string', 'max:50'],
            'hiragana' => ['required', 'string', 'max:100'],
            'meaning_id' => ['required', 'string', 'max:255'],
            'level' => ['required', 'string', 'max:20'],
            'topic_ids' => ['nullable', 'array'],
            'topic_ids.*' => ['exists:topics,id'],
            'romaji' => ['nullable', 'string', 'max:100'],
            'onyomi' => ['nullable', 'string', 'max:255'],
            'kunyomi' => ['nullable', 'string', 'max:255'],
            'stroke_count' => ['nullable', 'integer', 'min:1', 'max:60'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'kanji.required' => 'Huruf Kanji wajib diisi.',
            'hiragana.required' => 'Cara baca Hiragana wajib diisi.',
            'meaning_id.required' => 'Arti bahasa Indonesia wajib diisi.',
            'level.required' => 'Level bahasa wajib dipilih.',
        ]);

        $kanji->update($validated);

        if (!empty($validated['topic_ids'])) {
            $kanji->topics()->sync($validated['topic_ids']);
        } else {
            $kanji->topics()->detach();
        }

        return redirect()->route('sensei.kanjis.index')->with('success', "Huruf Kanji [{$kanji->kanji}] berhasil diperbarui!");
    }

    /**
     * Remove the specified Kanji.
     */
    public function destroy(Kanji $kanji): RedirectResponse
    {
        $char = $kanji->kanji;
        $kanji->topics()->detach();
        $kanji->delete();

        return redirect()->back()->with('success', "Kanji [{$char}] berhasil dihapus!");
    }
}