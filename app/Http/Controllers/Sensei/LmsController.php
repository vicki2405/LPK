<?php

namespace App\Http\Controllers\Sensei;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Vocabulary;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class LmsController extends Controller
{
    /**
     * Display LMS Kurikulum & Daftar Bab dengan Model Tabel, Filter & Pagination.
     */
    public function index(Request $request): Response
    {
        $courses = Course::withCount('chapters')
            ->orderBy('id', 'asc')
            ->get();

        $selectedCourseId = (int) $request->input('course_id', $courses->first()?->id);

        $perPage = (int) $request->input('per_page', 20);
        if (!in_array($perPage, [10, 20, 30, 50])) {
            $perPage = 20;
        }

        $query = Chapter::query()->withCount(['vocabularies', 'lessons']);

        if ($selectedCourseId) {
            $query->where('course_id', $selectedCourseId);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('chapter_number', $search);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            } elseif ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $sort = $request->input('sort', 'asc');
        $query->orderBy('chapter_number', $sort === 'desc' ? 'desc' : 'asc');

        $chapters = $query->paginate($perPage)->withQueryString();

        return Inertia::render('Sensei/Lms/Index', [
            'courses' => $courses,
            'selectedCourseId' => $selectedCourseId,
            'chapters' => $chapters,
            'filters' => [
                'course_id' => $selectedCourseId,
                'search' => $request->input('search', ''),
                'status' => $request->input('status', ''),
                'per_page' => $perPage,
                'sort' => $sort,
            ],
        ]);
    }

    /**
     * Display Halaman Khusus Editor Materi Bab (Full-Page Workspace).
     */
    public function show(int $id): Response
    {
        $chapter = Chapter::with([
            'course',
            'lessons' => fn($q) => $q->orderBy('order_index', 'asc')->orderBy('id', 'asc'),
            'learningIndicators' => fn($q) => $q->orderBy('name', 'asc'),
        ])->withCount('vocabularies')->findOrFail($id);

        return Inertia::render('Sensei/Lms/Show', [
            'chapter' => $chapter,
        ]);
    }

    /**
     * Store new Chapter, Lesson, or Vocabulary with Multimedia Uploads.
     */
    public function store(Request $request): RedirectResponse
    {
        $type = $request->input('type', 'chapter');

        if ($type === 'chapter') {
            $validated = $request->validate([
                'course_id' => 'required|exists:courses,id',
                'chapter_number' => 'required|integer|min:1',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_published' => 'nullable|boolean',
            ]);

            Chapter::create([
                'course_id' => $validated['course_id'],
                'chapter_number' => $validated['chapter_number'],
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'is_published' => $request->has('is_published') ? (bool) $request->input('is_published') : true,
            ]);

            return redirect()->back()->with('success', 'Bab baru berhasil ditambahkan!');
        }

        if ($type === 'lesson') {
            $validated = $request->validate([
                'chapter_id' => 'required|exists:chapters,id',
                'title' => 'required|string|max:255',
                'content_type' => 'required|in:text_grammar,video,pdf_handout,culture',
                'content_body' => 'required|string',
                'video_url' => 'nullable|string|max:255',
                'image_file' => 'nullable|image|max:5120', // Max 5MB
                'audio_file' => 'nullable|mimes:mp3,wav,ogg,m4a|max:10240', // Max 10MB
                'duration_minutes' => 'nullable|integer|min:1',
                'is_published' => 'required|boolean',
            ]);

            if ($request->hasFile('image_file')) {
                $path = $request->file('image_file')->store('lms/images', 'public');
                $validated['image_file'] = '/storage/' . $path;
            }

            if ($request->hasFile('audio_file')) {
                $path = $request->file('audio_file')->store('lms/audios', 'public');
                $validated['audio_file'] = '/storage/' . $path;
            }

            Lesson::create($validated);

            return redirect()->back()->with('success', 'Materi pembelajaran berhasil disimpan dan diterbitkan!');
        }

        if ($type === 'vocabulary') {
            $validated = $request->validate([
                'chapter_id' => 'required|exists:chapters,id',
                'kanji' => 'nullable|string|max:100',
                'hiragana' => 'required|string|max:100',
                'romaji' => 'nullable|string|max:100',
                'meaning_id' => 'required|string|max:255',
                'word_type' => 'required|string|max:50',
                'image_file' => 'nullable|image|max:5120',
                'audio_file' => 'nullable|mimes:mp3,wav,ogg,m4a|max:10240',
                'example_sentence_jp' => 'nullable|string',
                'example_sentence_id' => 'nullable|string',
            ]);

            if ($request->hasFile('image_file')) {
                $path = $request->file('image_file')->store('lms/vocab_images', 'public');
                $validated['image_file'] = '/storage/' . $path;
            }

            if ($request->hasFile('audio_file')) {
                $path = $request->file('audio_file')->store('lms/vocab_audios', 'public');
                $validated['audio_file'] = '/storage/' . $path;
            }

            Vocabulary::create($validated);

            return redirect()->back()->with('success', 'Kosakata (Kotoba) baru berhasil ditambahkan!');
        }

        return redirect()->back();
    }

    /**
     * Update Chapter, Lesson, or Vocabulary with Multimedia.
     */
    public function update(Request $request, int $id): RedirectResponse
    {
        $type = $request->input('type', 'chapter');

        if ($type === 'chapter') {
            $chapter = Chapter::findOrFail($id);
            $validated = $request->validate([
                'chapter_number' => 'required|integer|min:1',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'is_published' => 'nullable|boolean',
            ]);

            $chapter->update($validated);

            return redirect()->back()->with('success', 'Data Bab materi berhasil diperbarui!');
        }

        if ($type === 'lesson') {
            $lesson = Lesson::findOrFail($id);
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content_type' => 'required|in:text_grammar,video,pdf_handout,culture',
                'content_body' => 'required|string',
                'video_url' => 'nullable|string|max:255',
                'image_file' => 'nullable',
                'audio_file' => 'nullable',
                'duration_minutes' => 'nullable|integer|min:1',
                'is_published' => 'required|boolean',
            ]);

            if ($request->hasFile('image_file')) {
                $path = $request->file('image_file')->store('lms/images', 'public');
                $validated['image_file'] = '/storage/' . $path;
            } else {
                unset($validated['image_file']);
            }

            if ($request->hasFile('audio_file')) {
                $path = $request->file('audio_file')->store('lms/audios', 'public');
                $validated['audio_file'] = '/storage/' . $path;
            } else {
                unset($validated['audio_file']);
            }

            $lesson->update($validated);

            return redirect()->back()->with('success', 'Materi pembelajaran berhasil diperbarui!');
        }

        if ($type === 'vocabulary') {
            $vocab = Vocabulary::findOrFail($id);
            $validated = $request->validate([
                'kanji' => 'nullable|string|max:100',
                'hiragana' => 'required|string|max:100',
                'romaji' => 'nullable|string|max:100',
                'meaning_id' => 'required|string|max:255',
                'word_type' => 'required|string|max:50',
                'image_file' => 'nullable',
                'audio_file' => 'nullable',
                'example_sentence_jp' => 'nullable|string',
                'example_sentence_id' => 'nullable|string',
            ]);

            if ($request->hasFile('image_file')) {
                $path = $request->file('image_file')->store('lms/vocab_images', 'public');
                $validated['image_file'] = '/storage/' . $path;
            } else {
                unset($validated['image_file']);
            }

            if ($request->hasFile('audio_file')) {
                $path = $request->file('audio_file')->store('lms/vocab_audios', 'public');
                $validated['audio_file'] = '/storage/' . $path;
            } else {
                unset($validated['audio_file']);
            }

            $vocab->update($validated);

            return redirect()->back()->with('success', 'Kosakata (Kotoba) berhasil diperbarui!');
        }

        return redirect()->back();
    }

    /**
     * Delete Chapter, Lesson, or Vocabulary.
     */
    public function destroy(Request $request, int $id): RedirectResponse
    {
        $type = $request->input('type', 'chapter');

        if ($type === 'chapter') {
            Chapter::findOrFail($id)->delete();
            return redirect()->route('sensei.lms.index')->with('success', 'Bab materi berhasil dihapus!');
        }

        if ($type === 'lesson') {
            Lesson::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Materi pembelajaran berhasil dihapus!');
        }

        if ($type === 'vocabulary') {
            Vocabulary::findOrFail($id)->delete();
            return redirect()->back()->with('success', 'Kosakata (Kotoba) berhasil dihapus!');
        }

        return redirect()->back();
    }
}
