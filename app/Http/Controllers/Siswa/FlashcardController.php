<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\LanguageLevel;
use App\Models\Topic;
use App\Models\Vocabulary;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class FlashcardController extends Controller
{
    /**
     * Display interactive Kotoba Flashcard Gym & Quiz Hub with Topic Grouping.
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

        $selectedLevel = $request->input('level', 'N5');
        if ($selectedLevel !== 'ALL' && !isset($levels[$selectedLevel])) {
            $selectedLevel = array_keys($levels)[1] ?? 'ALL';
        }

        $selectedTopicId = $request->input('topic_id');
        $selectedCategory = $request->input('category');
        $selectedChapterId = $request->input('chapter_id');

        // Ambil daftar Topik Kosakata per Level
        $topicsQuery = Topic::forVocabulary()->withCount('vocabularies')->orderBy('sort_order', 'asc');
        if ($selectedLevel && $selectedLevel !== 'ALL') {
            $topicsQuery->where('level', $selectedLevel);
        }
        $topics = $topicsQuery->get();

        $vocabulariesQuery = Vocabulary::with(['chapter', 'topics']);

        if ($selectedLevel && $selectedLevel !== 'ALL') {
            $vocabulariesQuery->where('level', $selectedLevel);
        }

        if ($selectedTopicId && $selectedTopicId !== 'all') {
            $vocabulariesQuery->whereHas('topics', function ($q) use ($selectedTopicId) {
                $q->where('topics.id', $selectedTopicId);
            });
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

        $chapters = Chapter::where('is_published', true)
            ->withCount('vocabularies')
            ->orderBy('chapter_number', 'asc')
            ->get(['id', 'chapter_number', 'title', 'course_id']);

        $categories = Vocabulary::whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->pluck('category')
            ->toArray();

        return Inertia::render('Siswa/Flashcards/Index', [
            'levels' => $levels,
            'topics' => $topics,
            'selectedTopicId' => $selectedTopicId ? (int)$selectedTopicId : null,
            'chapters' => $chapters,
            'categories' => $categories,
            'selectedLevel' => $selectedLevel,
            'selectedCategory' => $selectedCategory,
            'selectedChapterId' => $selectedChapterId,
            'vocabularies' => $vocabularies,
        ]);
    }
}