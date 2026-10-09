<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LanguageLevel;
use App\Models\Kanji;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class LanguageLevelController extends Controller
{
    /**
     * Display a listing of language levels.
     */
    public function index(): Response
    {
        $languageLevels = LanguageLevel::orderBy('sort_order', 'asc')->orderBy('id', 'asc')->get();

        // Calculate usage counts for each level
        $levelsWithStats = $languageLevels->map(function ($lvl) {
            return array_merge($lvl->toArray(), [
                'kanjis_count' => Kanji::where('level', $lvl->code)->count(),
                'students_count' => User::where('target_language_level', 'like', "%{$lvl->code}%")->count(),
                'vocabularies_count' => Vocabulary::where('level', $lvl->code)->count(),
            ]);
        });

        return Inertia::render('Admin/Master/LanguageLevels/Index', [
            'languageLevels' => $levelsWithStats,
        ]);
    }

    /**
     * Store a newly created language level.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['nullable', 'string', 'max:20', 'unique:language_levels,code'],
            'name' => ['required', 'string', 'max:255'],
            'name_jp' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        if (empty($validated['code'])) {
            // Ekstrak pola standar jika ada di nama (contoh: N5, N4, N3, N2, N1, JFT_A2, dsb)
            if (preg_match('/\b(N[1-5]|JFT[_\-\s]?[A-C][1-2]?|[A-C][1-2])\b/i', $validated['name'], $matches)) {
                $baseCode = strtoupper(preg_replace('/[^A-Za-z0-9]/', '_', $matches[0]));
            } else {
                $baseCode = strtoupper(Str::slug($validated['name'], '_'));
            }
            $baseCode = substr($baseCode ?: 'LVL', 0, 15);
            $code = $baseCode;
            $counter = 1;
            while (LanguageLevel::where('code', $code)->exists()) {
                $code = substr($baseCode, 0, 15) . '_' . $counter++;
            }
            $validated['code'] = $code;
        } else {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        if (!isset($validated['sort_order'])) {
            $maxOrder = LanguageLevel::max('sort_order') ?? 0;
            $validated['sort_order'] = $maxOrder + 1;
        }

        LanguageLevel::create($validated);

        return redirect()->back()->with('success', "Level {$validated['name']} ({$validated['code']}) berhasil ditambahkan!");
    }

    /**
     * Update the specified language level.
     */
    public function update(Request $request, LanguageLevel $languageLevel): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['nullable', 'string', 'max:20', 'unique:language_levels,code,' . $languageLevel->id],
            'name' => ['required', 'string', 'max:255'],
            'name_jp' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['boolean'],
        ]);

        if (empty($validated['code'])) {
            $validated['code'] = $languageLevel->code;
        } else {
            $validated['code'] = strtoupper(trim($validated['code']));
        }

        $languageLevel->update($validated);

        return redirect()->back()->with('success', "Level {$languageLevel->name} berhasil diperbarui!");
    }

    /**
     * Remove the specified language level.
     */
    public function destroy(LanguageLevel $languageLevel): RedirectResponse
    {
        $name = $languageLevel->name;
        $code = $languageLevel->code;

        // Cek keterikatan data
        $hasKanji = Kanji::where('level', $code)->exists();
        $hasVocab = Vocabulary::where('level', $code)->exists();

        if ($hasKanji || $hasVocab) {
            return redirect()->back()->with('error', "Level {$name} ({$code}) tidak dapat dihapus karena masih digunakan di data Kanji atau Kosakata! Silakan nonaktifkan saja level ini.");
        }

        $languageLevel->delete();

        return redirect()->back()->with('success', "Level {$name} berhasil dihapus!");
    }
}