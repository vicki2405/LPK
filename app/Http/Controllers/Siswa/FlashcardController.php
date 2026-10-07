<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Vocabulary;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FlashcardController extends Controller
{
    /**
     * Display interactive Kotoba Flashcard Gym & Quiz Hub.
     */
    public function index(Request $request): Response
    {
        $levels = [
            'ALL' => 'Semua Level',
            'N5' => 'N5 (Dasar)',
            'N4' => 'N4 (Standar Kerja)',
            'N3' => 'N3 (Menengah)',
            'SSW_KAIGO' => 'SSW Kaigo',
            'SSW_FOOD' => 'SSW Pengolahan Makanan',
            'SSW_AGRICULTURE' => 'SSW Pertanian',
            'GENERAL' => 'Umum',
        ];

        $chapters = Chapter::where('is_published', true)
            ->withCount('vocabularies')
            ->orderBy('chapter_number', 'asc')
            ->get(['id', 'chapter_number', 'title', 'course_id']);

        $categories = Vocabulary::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->toArray();

        $selectedLevel = $request->input('level', 'ALL');
        $selectedCategory = $request->input('category');
        $selectedChapterId = $request->input('chapter_id');

        $vocabulariesQuery = Vocabulary::with('chapter');

        if ($selectedLevel && $selectedLevel !== 'ALL') {
            $vocabulariesQuery->where('level', $selectedLevel);
        }

        if ($selectedCategory) {
            $vocabulariesQuery->where('category', $selectedCategory);
        }

        if ($selectedChapterId) {
            if ($selectedChapterId === 'none') {
                $vocabulariesQuery->whereNull('chapter_id');
            } else {
                $vocabulariesQuery->where('chapter_id', $selectedChapterId);
            }
        }

        $vocabularies = $vocabulariesQuery->orderBy('id', 'asc')->get();

        return Inertia::render('Siswa/Flashcards/Index', [
            'levels' => $levels,
            'chapters' => $chapters,
            'categories' => $categories,
            'selectedLevel' => $selectedLevel,
            'selectedCategory' => $selectedCategory,
            'selectedChapterId' => $selectedChapterId,
            'vocabularies' => $vocabularies,
        ]);
    }
}
