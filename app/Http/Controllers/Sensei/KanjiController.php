<?php

namespace App\Http\Controllers\Sensei;

use App\Http\Controllers\Controller;
use App\Models\Kanji;
use App\Models\LanguageLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KanjiController extends Controller
{
    /**
     * Display listing of Kanjis.
     */
    public function index(Request $request): Response
    {
        $query = Kanji::query()->latest('id');

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('kanji', 'like', "%{$search}%")
                  ->orWhere('hiragana', 'like', "%{$search}%")
                  ->orWhere('meaning_id', 'like', "%{$search}%")
                  ->orWhere('romaji', 'like', "%{$search}%");
            });
        }

        if ($request->filled('level') && $request->level !== 'all') {
            $query->where('level', $request->level);
        }

        $kanjis = $query->paginate(24)->withQueryString();

        $languageLevels = LanguageLevel::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        $stats = [
            'total' => Kanji::count(),
            'total_n5' => Kanji::where('level', 'N5')->count(),
            'total_n4' => Kanji::where('level', 'N4')->count(),
        ];

        return Inertia::render('Sensei/Kanji/Index', [
            'kanjis' => $kanjis,
            'languageLevels' => $languageLevels,
            'filters' => [
                'search' => $request->search ?? '',
                'level' => $request->level ?? 'all',
            ],
            'stats' => $stats,
        ]);
    }

    /**
     * Show the form for creating a new Kanji.
     */
    public function create(): Response
    {
        $languageLevels = LanguageLevel::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        return Inertia::render('Sensei/Kanji/Create', [
            'languageLevels' => $languageLevels,
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
            'romaji' => ['nullable', 'string', 'max:100'],
            'stroke_count' => ['nullable', 'integer', 'min:1', 'max:60'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'kanji.required' => 'Huruf Kanji wajib diisi.',
            'hiragana.required' => 'Cara baca Hiragana wajib diisi.',
            'meaning_id.required' => 'Arti bahasa Indonesia wajib diisi.',
            'level.required' => 'Level bahasa wajib dipilih.',
        ]);

        $validated['created_by'] = $request->user()?->id;

        Kanji::create($validated);

        return redirect()->route('sensei.kanjis.index')->with('success', "Huruf Kanji [{$validated['kanji']}] berhasil ditambahkan!");
    }

    /**
     * Show the form for editing the specified Kanji.
     */
    public function edit(Kanji $kanji): Response
    {
        $languageLevels = LanguageLevel::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        return Inertia::render('Sensei/Kanji/Edit', [
            'kanji' => $kanji,
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
            'romaji' => ['nullable', 'string', 'max:100'],
            'stroke_count' => ['nullable', 'integer', 'min:1', 'max:60'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ], [
            'kanji.required' => 'Huruf Kanji wajib diisi.',
            'hiragana.required' => 'Cara baca Hiragana wajib diisi.',
            'meaning_id.required' => 'Arti bahasa Indonesia wajib diisi.',
            'level.required' => 'Level bahasa wajib dipilih.',
        ]);

        $kanji->update($validated);

        return redirect()->route('sensei.kanjis.index')->with('success', "Huruf Kanji [{$kanji->kanji}] berhasil diperbarui!");
    }

    /**
     * Remove the specified Kanji.
     */
    public function destroy(Kanji $kanji): RedirectResponse
    {
        $char = $kanji->kanji;
        $kanji->delete();

        return redirect()->back()->with('success', "Kanji [{$char}] berhasil dihapus!");
    }
}
