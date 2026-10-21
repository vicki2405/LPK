<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LmsController extends Controller
{
    /**
     * Display list of all published LMS courses and chapters for Siswa.
     */
    public function index(Request $request): Response
    {
        $courses = Course::where('is_published', true)
            ->with(['chapters' => function ($q) {
                $q->where('is_published', true)
                  ->withCount('vocabularies')
                  ->orderBy('chapter_number', 'asc');
            }])
            ->orderBy('id', 'asc')
            ->get();

        return Inertia::render('Siswa/Lms/Index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Display full-page reader for a single chapter.
     */
    public function show(Chapter $chapter): Response
    {
        $chapter->load(['course', 'learningIndicators', 'lessons' => fn($q) => $q->where('is_published', true)->orderBy('order_index')]);
        $chapter->loadCount('vocabularies');

        $otherChapters = Chapter::where('course_id', $chapter->course_id)
            ->where('is_published', true)
            ->orderBy('chapter_number', 'asc')
            ->get(['id', 'chapter_number', 'title']);

        $prevChapter = Chapter::where('course_id', $chapter->course_id)
            ->where('is_published', true)
            ->where('chapter_number', '<', $chapter->chapter_number)
            ->orderBy('chapter_number', 'desc')
            ->first(['id', 'chapter_number', 'title']);

        $nextChapter = Chapter::where('course_id', $chapter->course_id)
            ->where('is_published', true)
            ->where('chapter_number', '>', $chapter->chapter_number)
            ->orderBy('chapter_number', 'asc')
            ->first(['id', 'chapter_number', 'title']);

        return Inertia::render('Siswa/Lms/Show', [
            'chapter' => $chapter,
            'otherChapters' => $otherChapters,
            'prevChapter' => $prevChapter,
            'nextChapter' => $nextChapter,
        ]);
    }
}
