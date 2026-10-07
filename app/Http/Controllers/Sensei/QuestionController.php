<?php

namespace App\Http\Controllers\Sensei;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionOption;
use App\Models\Subject;
use App\Services\QuestionDocxService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class QuestionController extends Controller
{
    /**
     * Display Hub Master Bank Soal CBT per Mata Pelajaran (Mapel).
     */
    public function index(Request $request): Response
    {
        $selectedSubjectId = $request->input('subject_id', '');
        $selectedStatus = $request->input('status', '');
        $search = $request->input('search', '');

        // 1. Daftar Mata Pelajaran (Mapel) beserta jumlah paket bank soal
        $subjects = Subject::withCount('questionBanks')
            ->orderBy('order_index')
            ->orderBy('name')
            ->get();

        // 2. Query Master Bank Soal (QuestionBanks)
        $packagesQuery = QuestionBank::with([
                'subject:id,name,code',
                'creator:id,name',
            ])
            ->withCount(['questions', 'exams'])
            ->latest();

        if ($selectedSubjectId) {
            $packagesQuery->where('subject_id', $selectedSubjectId);
        }

        if ($selectedStatus === 'active') {
            $packagesQuery->where('is_active', true);
        } elseif ($selectedStatus === 'inactive') {
            $packagesQuery->where('is_active', false);
        }

        if ($search) {
            $packagesQuery->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhereHas('subject', fn($sq) => $sq->where('name', 'like', "%{$search}%"));
            });
        }

        $packages = $packagesQuery->paginate(15)->withQueryString();

        // 3. Batch/Kelas aktif untuk referensi
        $batches = Batch::where('status', 'active')
            ->select('id', 'name', 'code')
            ->get();

        // 4. Statistik Ringkas
        $totalQuestions = DB::table('question_bank_questions')->count();
        $totalActivePackages = QuestionBank::where('is_active', true)->count();

        return Inertia::render('Sensei/Questions/Index', [
            'subjects' => $subjects,
            'packages' => $packages,
            'batches' => $batches,
            'filters' => [
                'subject_id' => $selectedSubjectId ? (int) $selectedSubjectId : '',
                'status' => $selectedStatus,
                'search' => $search,
            ],
            'stats' => [
                'total_subjects' => $subjects->count(),
                'total_packages' => QuestionBank::count(),
                'total_active' => $totalActivePackages,
                'total_questions' => $totalQuestions,
            ],
        ]);
    }

    /**
     * Store a newly created Subject (Mata Pelajaran).
     */
    public function storeSubject(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:subjects,code',
            'description' => 'nullable|string|max:255',
        ]);

        $maxOrder = Subject::max('order_index') ?? 0;

        Subject::create([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'order_index' => $maxOrder + 1,
            'is_active' => true,
            'created_by' => Auth::id(),
        ]);

        return redirect()->back()->with('success', "Mata Pelajaran \"{$validated['name']}\" berhasil ditambahkan!");
    }

    /**
     * Update an existing Subject.
     */
    public function updateSubject(Request $request, Subject $subject): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => "required|string|max:50|unique:subjects,code,{$subject->id}",
            'description' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ]);

        $subject->update([
            'name' => $validated['name'],
            'code' => strtoupper($validated['code']),
            'description' => $validated['description'] ?? null,
            'is_active' => $request->has('is_active') ? (bool) $validated['is_active'] : $subject->is_active,
        ]);

        return redirect()->back()->with('success', "Mata Pelajaran \"{$subject->name}\" berhasil diperbarui!");
    }

    /**
     * Delete a Subject.
     */
    public function destroySubject(Subject $subject): RedirectResponse
    {
        if ($subject->questionBanks()->count() > 0) {
            return redirect()->back()->withErrors([
                'error' => "Mata Pelajaran \"{$subject->name}\" tidak dapat dihapus karena masih memiliki {$subject->questionBanks()->count()} paket bank soal aktif. Hapus atau pindahkan paket bank soal terlebih dahulu.",
            ]);
        }

        $name = $subject->name;
        $subject->delete();

        return redirect()->back()->with('success', "Mata Pelajaran \"{$name}\" berhasil dihapus.");
    }

    /**
     * Store a newly created Master Question Bank Package (Paket Bank Soal per Mapel).
     */
    public function storePackage(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:question_banks,code',
            'level' => 'nullable|in:N5,N4,N3,N2,N1,JFT_A2',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'passing_score' => 'required|integer|min:0|max:1000',
            'max_score' => 'nullable|integer|min:1',
            'display_mode' => 'nullable|in:formal,game',
            'description' => 'nullable|string',
        ]);

        $code = !empty($validated['code']) 
            ? strtoupper($validated['code']) 
            : 'PKT-' . strtoupper(Str::random(6));

        $package = QuestionBank::create([
            'created_by' => Auth::id(),
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'code' => $code,
            'level' => $validated['level'] ?? 'N4',
            'duration_minutes' => $validated['duration_minutes'],
            'passing_score' => $validated['passing_score'],
            'max_score' => $validated['max_score'] ?? 180,
            'display_mode' => $validated['display_mode'] ?? 'formal',
            'description' => $validated['description'] ?? null,
            'is_active' => true,
        ]);

        return redirect()->route('sensei.questions.manage-package', $package->id)
            ->with('success', "Paket Bank Soal \"{$package->title}\" berhasil dibuat! Silakan mulai menulis butir-butir soal.");
    }

    /**
     * Update an existing Master Question Bank Package.
     */
    public function updatePackage(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'code' => "nullable|string|max:50|unique:question_banks,code,{$questionBank->id}",
            'level' => 'nullable|in:N5,N4,N3,N2,N1,JFT_A2',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'passing_score' => 'required|integer|min:0|max:1000',
            'max_score' => 'nullable|integer|min:1',
            'display_mode' => 'nullable|in:formal,game',
            'description' => 'nullable|string',
        ]);

        $code = !empty($validated['code']) ? strtoupper($validated['code']) : $questionBank->code;

        $questionBank->update([
            'subject_id' => $validated['subject_id'],
            'title' => $validated['title'],
            'code' => $code,
            'level' => $validated['level'] ?? $questionBank->level,
            'duration_minutes' => $validated['duration_minutes'],
            'passing_score' => $validated['passing_score'],
            'max_score' => $validated['max_score'] ?? $questionBank->max_score,
            'display_mode' => $validated['display_mode'] ?? $questionBank->display_mode,
            'description' => $validated['description'] ?? null,
        ]);

        Cache::forget("cbt_qb_questions_{$questionBank->id}");

        return redirect()->back()->with('success', "Pengaturan Master Bank Soal \"{$questionBank->title}\" berhasil diperbarui!");
    }

    /**
     * Delete a Master Question Bank Package.
     */
    public function destroyPackage(QuestionBank $questionBank): RedirectResponse
    {
        $title = $questionBank->title;
        Cache::forget("cbt_qb_questions_{$questionBank->id}");
        
        // Lepaskan pivot questions
        $questionBank->questions()->detach();
        $questionBank->delete();

        return redirect()->route('sensei.questions.index')
            ->with('success', "Paket Bank Soal \"{$title}\" berhasil dihapus.");
    }

    /**
     * Toggle Active status of a Question Bank Package.
     */
    public function togglePublishPackage(QuestionBank $questionBank): RedirectResponse
    {
        $newStatus = !$questionBank->is_active;
        $questionBank->update(['is_active' => $newStatus]);

        Cache::forget("cbt_qb_questions_{$questionBank->id}");

        $statusText = $newStatus 
            ? 'diaktifkan sebagai arsip bank soal aktif.' 
            : 'dinonaktifkan (arsip tersimpan).';

        return redirect()->back()->with('success', "Paket Bank Soal \"{$questionBank->title}\" berhasil {$statusText}");
    }

    /**
     * Lembar Penulisan & Manajemen Butir Soal di dalam Master Bank Soal.
     */
    public function managePackage(QuestionBank $questionBank): Response
    {
        $questionBank->load([
            'subject:id,name,code',
            'creator:id,name',
            'questions' => function ($q) {
                $q->with(['options', 'category:id,name,code,level,level_code,section_type'])
                  ->orderBy('question_bank_questions.order_index', 'asc')
                  ->orderBy('questions.id', 'asc');
            },
        ]);

        $subjects = Subject::active()->get(['id', 'name', 'code']);
        $batches = Batch::where('status', 'active')->get(['id', 'name', 'code']);
        $learningIndicators = \App\Models\QuestionCategory::orderBy('section_type')
            ->orderBy('level_code')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'level', 'level_code', 'section_type']);

        return Inertia::render('Sensei/Questions/Manage', [
            'package' => $questionBank,
            'subjects' => $subjects,
            'batches' => $batches,
            'learningIndicators' => $learningIndicators,
        ]);
    }

    /**
     * Tambah Butir Soal Baru langsung ke dalam Master Bank Soal.
     */
    public function storeItem(Request $request, QuestionBank $questionBank): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'section_type' => 'nullable|in:moji_goi,bunpou,dokkai,choukai',
            'question_category_id' => 'nullable|exists:question_categories,id',
            'instruction' => 'nullable|string',
            'reading_passage' => 'nullable|string',
            'score_points' => 'nullable|numeric|min:0.1',
            'explanation' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg,m4a|max:15360',
            'options' => 'required|array|min:2',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required',
        ]);

        $imageUrl = null;
        $audioUrl = null;

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('questions/images', 'public');
            $imageUrl = '/storage/' . $path;
        }

        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('questions/audios', 'public');
            $audioUrl = '/storage/' . $path;
        }

        $sectionType = $validated['section_type'] ?? ($audioUrl ? 'choukai' : 'bunpou');

        DB::transaction(function () use ($validated, $questionBank, $imageUrl, $audioUrl, $sectionType) {
            $question = Question::create([
                'created_by' => Auth::id(),
                'question_category_id' => $validated['question_category_id'] ?? null,
                'level' => $questionBank->level ?? 'N4',
                'section_type' => $sectionType,
                'question_text' => $validated['question_text'],
                'instruction' => $validated['instruction'] ?? null,
                'reading_passage' => $validated['reading_passage'] ?? null,
                'image_url' => $imageUrl,
                'audio_url' => $audioUrl,
                'score_points' => $validated['score_points'] ?? 1.0,
                'explanation' => $validated['explanation'] ?? null,
            ]);

            foreach ($validated['options'] as $idx => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_key' => chr(65 + $idx), // A, B, C, D...
                    'option_text' => $opt['option_text'],
                    'is_correct' => filter_var($opt['is_correct'], FILTER_VALIDATE_BOOLEAN),
                ]);
            }

            $maxOrder = $questionBank->questions()->max('question_bank_questions.order_index') ?? 0;
            $questionBank->questions()->attach($question->id, [
                'section_type' => $sectionType,
                'order_index' => $maxOrder + 1,
            ]);

            // Sinkronkan juga butir soal ini ke jadwal ujian CBT aktif yang memakai bank soal ini
            foreach ($questionBank->exams as $exam) {
                $examMaxOrder = $exam->questions()->max('exam_questions.order_index') ?? 0;
                $exam->questions()->syncWithoutDetaching([
                    $question->id => [
                        'section_type' => $sectionType,
                        'order_index' => $examMaxOrder + 1,
                    ],
                ]);
                Cache::forget("cbt_exam_questions_{$exam->id}");
                Cache::forget("cbt_exam_questions_{$exam->id}_game");
                Cache::forget("cbt_exam_questions_{$exam->id}_formal");
                Cache::forget("cbt_exam_questions_{$exam->id}_secure");
            }
        });

        Cache::forget("cbt_qb_questions_{$questionBank->id}");

        return redirect()->back()->with('success', 'Butir soal baru berhasil ditambahkan ke dalam Master Bank Soal!');
    }

    /**
     * Perbarui Butir Soal yang ada di dalam Master Bank Soal.
     */
    public function updateItem(Request $request, QuestionBank $questionBank, Question $question): RedirectResponse
    {
        $validated = $request->validate([
            'question_text' => 'required|string',
            'section_type' => 'nullable|in:moji_goi,bunpou,dokkai,choukai',
            'question_category_id' => 'nullable|exists:question_categories,id',
            'instruction' => 'nullable|string',
            'reading_passage' => 'nullable|string',
            'score_points' => 'nullable|numeric|min:0.1',
            'explanation' => 'nullable|string',
            'image_file' => 'nullable|image|max:5120',
            'audio_file' => 'nullable|mimes:mp3,wav,ogg,m4a|max:15360',
            'options' => 'required|array|min:2',
            'options.*.id' => 'nullable|integer',
            'options.*.option_text' => 'required|string',
            'options.*.is_correct' => 'required',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('questions/images', 'public');
            $question->image_url = '/storage/' . $path;
        }

        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('questions/audios', 'public');
            $question->audio_url = '/storage/' . $path;
        }

        $sectionType = $validated['section_type'] ?? ($question->audio_url ? 'choukai' : ($question->section_type ?? 'bunpou'));

        $question->question_text = $validated['question_text'];
        $question->section_type = $sectionType;
        $question->question_category_id = $validated['question_category_id'] ?? null;
        $question->instruction = $validated['instruction'] ?? null;
        $question->reading_passage = $validated['reading_passage'] ?? null;
        $question->score_points = $validated['score_points'] ?? 1.0;
        $question->explanation = $validated['explanation'] ?? null;
        $question->save();

        // Update pivot section_type
        $questionBank->questions()->updateExistingPivot($question->id, [
            'section_type' => $sectionType,
        ]);

        foreach ($questionBank->exams as $exam) {
            $exam->questions()->updateExistingPivot($question->id, [
                'section_type' => $sectionType,
            ]);
        }

        // Update / re-create options
        DB::transaction(function () use ($validated, $question) {
            $question->options()->delete();
            foreach ($validated['options'] as $idx => $opt) {
                QuestionOption::create([
                    'question_id' => $question->id,
                    'option_key' => chr(65 + $idx),
                    'option_text' => $opt['option_text'],
                    'is_correct' => filter_var($opt['is_correct'], FILTER_VALIDATE_BOOLEAN),
                ]);
            }
        });

        Cache::forget("cbt_qb_questions_{$questionBank->id}");
        foreach ($questionBank->exams as $exam) {
            Cache::forget("cbt_exam_questions_{$exam->id}");
            Cache::forget("cbt_exam_questions_{$exam->id}_game");
            Cache::forget("cbt_exam_questions_{$exam->id}_formal");
            Cache::forget("cbt_exam_questions_{$exam->id}_secure");
        }

        return redirect()->back()->with('success', 'Butir soal berhasil diperbarui!');
    }

    /**
     * Hapus Butir Soal dari Master Bank Soal.
     */
    public function destroyItem(QuestionBank $questionBank, Question $question): RedirectResponse
    {
        DB::transaction(function () use ($questionBank, $question) {
            $questionBank->questions()->detach($question->id);
            foreach ($questionBank->exams as $exam) {
                $exam->questions()->detach($question->id);
                Cache::forget("cbt_exam_questions_{$exam->id}");
                Cache::forget("cbt_exam_questions_{$exam->id}_game");
                Cache::forget("cbt_exam_questions_{$exam->id}_formal");
                Cache::forget("cbt_exam_questions_{$exam->id}_secure");
            }
            $question->options()->delete();
            $question->delete();
        });

        Cache::forget("cbt_qb_questions_{$questionBank->id}");

        return redirect()->back()->with('success', 'Butir soal berhasil dihapus dari Master Bank Soal.');
    }

    /**
     * Preview CBT Simulation directly from Master Question Bank.
     */
    public function previewPackage(QuestionBank $questionBank): Response
    {
        $questions = $questionBank->questions()
            ->with(['category:id,name,section_type', 'options:id,question_id,option_key,option_text,option_image,is_correct'])
            ->orderBy('question_bank_questions.order_index', 'asc')
            ->orderBy('questions.id', 'asc')
            ->get()
            ->map(function ($q, $index) {
                return [
                    'id' => $q->id,
                    'number' => $index + 1,
                    'level' => $q->level,
                    'score_points' => (float) ($q->score_points ?? 1),
                    'category_id' => $q->question_category_id,
                    'category_name' => $q->category?->name ?? 'Umum / Standar',
                    'section_type' => $q->section_type ?? ($q->category?->section_type ?? 'bunpou'),
                    'instruction' => $q->instruction,
                    'question_text' => $q->question_text,
                    'audio_url' => $q->audio_url,
                    'audio_play_limit' => 99,
                    'passage_text' => $q->reading_passage,
                    'image_url' => $q->image_url,
                    'options' => $q->options->map(fn($opt) => [
                        'id' => $opt->id,
                        'option_key' => $opt->option_key,
                        'option_text' => $opt->option_text,
                        'option_image' => $opt->option_image,
                        'is_correct' => (bool) $opt->is_correct,
                    ])->values(),
                ];
            })->values()->all();

        return Inertia::render('Siswa/Cbt/Show', [
            'session' => [
                'id' => 0,
                'status' => 'in_progress',
                'violation_count' => 0,
                'is_disqualified' => false,
                'started_at' => now(),
                'expires_at' => now()->addMinutes($questionBank->duration_minutes),
                'remaining_seconds' => $questionBank->duration_minutes * 60,
                'is_preview' => true,
            ],
            'exam' => [
                'id' => $questionBank->id,
                'title' => "[PREVIEW MASTER BANK] " . $questionBank->title,
                'code' => $questionBank->code,
                'level' => $questionBank->level,
                'duration_minutes' => $questionBank->duration_minutes,
                'passing_score' => $questionBank->passing_score,
                'max_score' => $questionBank->max_score ?? 180,
                'display_mode' => $questionBank->display_mode ?? 'formal',
                'allow_student_mode_switch' => true,
            ],
            'questions' => $questions,
            'existingAnswers' => (object)[],
        ]);
    }

    /**
     * Unduh Template Microsoft Word (.docx) resmi standar LPK berbasis Tabel.
     */
    public function downloadTemplate(QuestionDocxService $docxService)
    {
        $filePath = $docxService->generateTemplate();
        return response()->download($filePath, 'Template_Soal_CBT_LPK.docx')->deleteFileAfterSend(true);
    }

    /**
     * Ekspor seluruh butir soal dalam paket ke format Word (.docx) siap cetak.
     */
    public function exportDocx(QuestionBank $questionBank, QuestionDocxService $docxService)
    {
        $filePath = $docxService->exportPackageToDocx($questionBank);
        $filename = 'Naskah_Soal_' . Str::slug($questionBank->code) . '.docx';
        return response()->download($filePath, $filename)->deleteFileAfterSend(true);
    }

    /**
     * Impor butir-butir soal dari file Word (.docx) berbasis Tabel.
     */
    public function importDocx(Request $request, QuestionBank $questionBank, QuestionDocxService $docxService): RedirectResponse
    {
        $request->validate([
            'file' => 'required|file|mimes:docx|max:25600', // Maks 25MB (termasuk media gambar)
        ]);

        try {
            $result = $docxService->importFromDocx($request->file('file'), $questionBank, Auth::id());
            Cache::forget("cbt_qb_questions_{$questionBank->id}");

            if ($result['imported_count'] > 0) {
                $msg = "Berhasil mengimpor {$result['imported_count']} butir soal ke dalam paket!";
                if ($result['failed_count'] > 0) {
                    $msg .= " ({$result['failed_count']} tabel dilewati: " . implode('; ', array_slice($result['errors'], 0, 3)) . ")";
                }
                return redirect()->back()->with('success', $msg);
            } else {
                return redirect()->back()->withErrors([
                    'error' => 'Gagal mengimpor soal: ' . implode('; ', $result['errors'] ?? ['Format tabel Word tidak sesuai template LPK']),
                ]);
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'error' => 'Terjadi kesalahan saat memproses file Word: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Impor cepat dari teks terstruktur (Aiken format).
     */
    public function importText(Request $request, QuestionBank $questionBank, QuestionDocxService $docxService): RedirectResponse
    {
        $validated = $request->validate([
            'raw_text' => 'required|string|min:10',
        ]);

        try {
            $result = $docxService->importFromAikenText($validated['raw_text'], $questionBank, Auth::id());
            Cache::forget("cbt_qb_questions_{$questionBank->id}");

            if ($result['imported_count'] > 0) {
                return redirect()->back()->with('success', "Berhasil mengimpor {$result['imported_count']} butir soal dari teks!");
            } else {
                return redirect()->back()->withErrors([
                    'error' => 'Tidak ada soal yang berhasil dibaca. Pastikan format penulisan memuat Pertanyaan, Pilihan A-D, dan KUNCI/ANSWER.',
                ]);
            }
        } catch (\Throwable $e) {
            return redirect()->back()->withErrors([
                'error' => 'Gagal memproses teks: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * Fallback route create: arahkan ke index dengan instruksi buat paket.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('sensei.questions.index');
    }
}
