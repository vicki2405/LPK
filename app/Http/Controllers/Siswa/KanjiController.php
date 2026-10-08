<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Kanji;
use App\Models\LanguageLevel;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class KanjiController extends Controller
{
    /**
     * Display student interactive Kanji Flashcard Gym & Quiz Hub.
     */
    public function index(Request $request): Response
    {
        $dbLevels = LanguageLevel::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $levels = ['ALL' => 'Semua Level'];
        foreach ($dbLevels as $lvl) {
            $levels[$lvl->code] = $lvl->name;
        }

        if (count($levels) <= 1) {
            $levels = [
                'ALL' => 'Semua Level',
                'N5' => 'JLPT N5 (Tingkat Dasar)',
                'N4' => 'JLPT N4 & JFT-Basic A2 (Standar Kerja)',
                'N3' => 'JLPT N3 (Tingkat Menengah)',
            ];
        }

        $selectedLevel = $request->input('level', 'ALL');

        $query = Kanji::query()->orderBy('id', 'asc');

        if ($selectedLevel && $selectedLevel !== 'ALL') {
            $query->where('level', $selectedLevel);
        }

        $kanjis = $query->get();

        $stats = [
            'total' => Kanji::count(),
            'total_n5' => Kanji::where('level', 'N5')->count(),
            'total_n4' => Kanji::where('level', 'N4')->count(),
        ];

        return Inertia::render('Siswa/Kanji/Index', [
            'levels' => $levels,
            'selectedLevel' => $selectedLevel,
            'kanjis' => $kanjis,
            'stats' => $stats,
        ]);
    }
}
