<?php

namespace App\Http\Controllers\Sensei;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\Chapter;
use App\Models\Exam;
use App\Models\ExamSession;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\QuestionLevel;
use App\Models\QuestionBank;
use App\Models\Subject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ExamController extends Controller
{
    /**
     * Display listing of CBT Exams.
     */
    public function index(): Response
    {
        $exams = Exam::with([
                'subject:id,name,code',
                'questionBank:id,title,code,level,duration_minutes,passing_score,max_score',
                'batch:id,name,code', 
                'questions:id,level,score_points', 
                'creator:id,name',
                'sessions.user:id,name,email',
                'sessions.user.batches:id,name',
            ])
            ->withCount(['sessions', 'questions'])
            ->latest()
            ->get();

        $subjects = Subject::active()->orderBy('order_index')->get(['id', 'name', 'code']);
        $questionBanks = QuestionBank::with('subject:id,name,code')
            ->withCount('questions')
            ->active()
            ->orderBy('title')
            ->get();
        $batches = Batch::where('status', 'active')->select('id', 'name', 'code')->get();
        $levels = QuestionLevel::active()->get();

        return Inertia::render('Sensei/Exams/Index', [
            'exams' => $exams,
            'subjects' => $subjects,
            'questionBanks' => $questionBanks,
            'batches' => $batches,
            'levels' => $levels,
        ]);
    }

    /**
     * Store newly created CBT Exam.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:exams,code',
            'question_bank_id' => 'nullable|exists:question_banks,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'level' => 'nullable|in:N5,N4,N3,N2,N1,JFT_A2',
            'exam_type' => 'nullable|in:jlpt_simulation,jft_simulation,chapter_quiz,daily_test',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'passing_score' => 'required|integer|min:1',
            'max_score' => 'nullable|integer|min:1',
            'batch_id' => 'nullable|exists:batches,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'is_published' => 'required|boolean',
            'description' => 'nullable|string',
            'display_mode' => 'nullable|in:formal,game',
            'allow_student_mode_switch' => 'nullable|boolean',
            'question_ids' => 'nullable|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $code = !empty($validated['code']) 
            ? strtoupper($validated['code']) 
            : 'CBT-' . strtoupper(Str::random(6));

        $exam = Exam::create([
            'title' => $validated['title'],
            'code' => $code,
            'question_bank_id' => $validated['question_bank_id'] ?? null,
            'subject_id' => $validated['subject_id'] ?? null,
            'level' => $validated['level'] ?? 'N4',
            'exam_type' => $validated['exam_type'] ?? 'jlpt_simulation',
            'duration_minutes' => $validated['duration_minutes'],
            'passing_score' => $validated['passing_score'],
            'max_score' => $validated['max_score'] ?? 180,
            'batch_id' => $validated['batch_id'] ?? null,
            'start_time' => $validated['start_time'] ?? null,
            'end_time' => $validated['end_time'] ?? null,
            'is_published' => $validated['is_published'],
            'display_mode' => $validated['display_mode'] ?? 'formal',
            'allow_student_mode_switch' => $validated['allow_student_mode_switch'] ?? false,
            'description' => $validated['description'] ?? null,
            'created_by' => Auth::id(),
        ]);

        if (!empty($validated['question_bank_id'])) {
            $bank = QuestionBank::with('questions')->find($validated['question_bank_id']);
            if ($bank && $bank->questions->isNotEmpty()) {
                $syncData = [];
                foreach ($bank->questions as $q) {
                    $syncData[$q->id] = [
                        'section_type' => $q->pivot->section_type ?? $q->section_type ?? 'bunpou',
                        'order_index' => $q->pivot->order_index ?? 1,
                    ];
                }
                $exam->questions()->sync($syncData);
            }
        } elseif (!empty($validated['question_ids'])) {
            $exam->questions()->sync($validated['question_ids']);
        }

        return redirect()->back()->with('success', 'Paket Jadwal Ujian CBT berhasil dibuat!');
    }

    /**
     * Update specified CBT Exam.
     */
    public function update(Request $request, Exam $exam): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:exams,code,' . $exam->id,
            'question_bank_id' => 'nullable|exists:question_banks,id',
            'subject_id' => 'nullable|exists:subjects,id',
            'level' => 'nullable|in:N5,N4,N3,N2,N1,JFT_A2',
            'exam_type' => 'nullable|in:jlpt_simulation,jft_simulation,chapter_quiz,daily_test',
            'duration_minutes' => 'required|integer|min:5|max:300',
            'passing_score' => 'required|integer|min:1',
            'max_score' => 'nullable|integer|min:1',
            'batch_id' => 'nullable|exists:batches,id',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
            'is_published' => 'required|boolean',
            'description' => 'nullable|string',
            'display_mode' => 'nullable|in:formal,game',
            'allow_student_mode_switch' => 'nullable|boolean',
            'question_ids' => 'nullable|array',
            'question_ids.*' => 'exists:questions,id',
        ]);

        $validated['code'] = !empty($validated['code']) ? strtoupper($validated['code']) : $exam->code;
        $validated['level'] = $validated['level'] ?? $exam->level ?? 'N4';
        $validated['exam_type'] = $validated['exam_type'] ?? $exam->exam_type ?? 'jlpt_simulation';
        $validated['max_score'] = $validated['max_score'] ?? $exam->max_score ?? 180;
        $validated['display_mode'] = $validated['display_mode'] ?? $exam->display_mode ?? 'formal';

        $oldBankId = $exam->question_bank_id;
        $exam->update($validated);

        // Jika question_bank_id baru dipilih atau berganti, sinkronkan butir soal dari bank soal
        if (!empty($validated['question_bank_id']) && $validated['question_bank_id'] != $oldBankId) {
            $bank = QuestionBank::with('questions')->find($validated['question_bank_id']);
            if ($bank && $bank->questions->isNotEmpty()) {
                $syncData = [];
                foreach ($bank->questions as $q) {
                    $syncData[$q->id] = [
                        'section_type' => $q->pivot->section_type ?? $q->section_type ?? 'bunpou',
                        'order_index' => $q->pivot->order_index ?? 1,
                    ];
                }
                $exam->questions()->sync($syncData);
            }
        } elseif (array_key_exists('question_ids', $validated) && is_array($validated['question_ids'])) {
            $exam->questions()->sync($validated['question_ids']);
        }

        Cache::forget("cbt_exam_questions_{$exam->id}");
        Cache::forget("cbt_exam_questions_{$exam->id}_game");
        Cache::forget("cbt_exam_questions_{$exam->id}_formal");
        Cache::forget("cbt_exam_questions_{$exam->id}_secure");
        Cache::forget("exam_mode_{$exam->id}");

        return redirect()->back()->with('success', 'Paket Jadwal Ujian CBT berhasil diperbarui!');
    }

    /**
     * Toggle publish status.
     */
    public function togglePublish(Exam $exam): RedirectResponse
    {
        $exam->update(['is_published' => !$exam->is_published]);
        Cache::forget("cbt_exam_questions_{$exam->id}");
        Cache::forget("cbt_exam_questions_{$exam->id}_secure");
        Cache::forget("exam_mode_{$exam->id}");
        $statusText = $exam->is_published ? 'diaktifkan dan dapat dikerjakan siswa!' : 'dinonaktifkan!';
        return redirect()->back()->with('success', "Ujian {$exam->title} berhasil {$statusText}");
    }

    /**
     * Toggle display mode (formal vs game) from Sensei Exam Hub.
     */
    public function toggleDisplayMode(Exam $exam): RedirectResponse
    {
        $newMode = $exam->display_mode === 'game' ? 'formal' : 'game';
        $exam->update(['display_mode' => $newMode]);
        Cache::forget("cbt_exam_questions_{$exam->id}_game");
        Cache::forget("cbt_exam_questions_{$exam->id}_formal");
        Cache::forget("exam_mode_{$exam->id}");
        $modeLabel = $newMode === 'game' ? '🎮 Mode Game Petualangan' : '🏛️ Mode Formal JFT/JLPT';
        return redirect()->back()->with('success', "Mode tampilan {$exam->title} berhasil diubah ke {$modeLabel}!");
    }

    /**
     * Live Preview of CBT Exam for Sensei without affecting database.
     */
    public function preview(Exam $exam): Response
    {
        $questions = $exam->questions()
            ->with(['category:id,name,section_type', 'options:id,question_id,option_key,option_text,option_image,is_correct'])
            ->orderBy('exam_questions.order_index', 'asc')
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
                    'audio_play_limit' => 99, // Unlimited for Sensei preview
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

        $dbLeaderboard = ExamSession::where('exam_id', $exam->id)
            ->whereNotNull('total_score')
            ->with('user:id,name')
            ->orderByDesc('total_score')
            ->orderBy('submitted_at', 'asc')
            ->limit(10)
            ->get()
            ->map(fn($s) => [
                'id' => $s->id,
                'user_id' => $s->user_id,
                'name' => $s->user?->name ?? 'Peserta CBT',
                'score' => (int) $s->total_score,
                'isCurrentUser' => false,
            ])->values()->all();

        return Inertia::render('Siswa/Cbt/Show', [
            'session' => [
                'id' => 0,
                'status' => 'in_progress',
                'violation_count' => 0,
                'is_disqualified' => false,
                'started_at' => now(),
                'expires_at' => now()->addMinutes($exam->duration_minutes),
                'remaining_seconds' => $exam->duration_minutes * 60,
                'is_preview' => true,
            ],
            'exam' => [
                'id' => $exam->id,
                'title' => "[PREVIEW SENSEI] " . $exam->title,
                'code' => $exam->code,
                'level' => $exam->level,
                'duration_minutes' => $exam->duration_minutes,
                'passing_score' => $exam->passing_score,
                'max_score' => $exam->max_score,
                'display_mode' => $exam->display_mode ?? 'formal',
                'allow_student_mode_switch' => (bool) ($exam->allow_student_mode_switch ?? false),
            ],
            'questions' => $questions,
            'existingAnswers' => (object)[],
            'initialLeaderboard' => $dbLeaderboard,
        ]);
    }

    /**
     * Delete CBT Exam schedule.
     * Master Bank Soal and its questions remain completely intact.
     */
    public function destroy(Exam $exam): RedirectResponse
    {
        Cache::forget("cbt_exam_questions_{$exam->id}");
        Cache::forget("cbt_exam_questions_{$exam->id}_game");
        Cache::forget("cbt_exam_questions_{$exam->id}_formal");
        Cache::forget("cbt_exam_questions_{$exam->id}_secure");
        Cache::forget("exam_mode_{$exam->id}");

        DB::transaction(function () use ($exam) {
            $exam->questions()->detach();
            $exam->delete();
        });

        return redirect()->back()->with('success', 'Jadwal Ujian CBT berhasil dihapus! Master Bank Soal tetap tersimpan aman.');
    }

    /**
     * Display live monitoring for CBT exam.
     */
    public function monitoring(Exam $exam): RedirectResponse
    {
        return redirect()->route('sensei.exams.index');
    }

    /**
     * Disqualify student session due to cheating / AI searching.
     */
    public function disqualifySession(ExamSession $session): RedirectResponse
    {
        $logs = $session->violation_logs ?? [];
        $logs[] = [
            'type' => 'manual_disqualification',
            'message' => 'Didiskualifikasi oleh Sensei pengawas ujian karena terbukti melakukan kecurangan.',
            'timestamp' => now()->toIso8601String(),
            'time_formatted' => now()->format('H:i:s - d M Y'),
        ];

        $session->update([
            'is_disqualified' => true,
            'is_passed' => false,
            'status' => 'cancelled',
            'violation_logs' => $logs,
        ]);

        $studentName = $session->user?->name ?? 'Siswa';
        return redirect()->back()->with('success', "Peserta {$studentName} telah resmi didiskualifikasi dari ujian ini.");
    }

    /**
     * Reset violation warnings for student session (Dispensasi).
     */
    public function resetViolations(ExamSession $session): RedirectResponse
    {
        $logs = $session->violation_logs ?? [];
        $logs[] = [
            'type' => 'violation_reset',
            'message' => 'Peringatan pelanggaran direset oleh Sensei (diberikan dispensasi/klarifikasi kendala teknis).',
            'timestamp' => now()->toIso8601String(),
            'time_formatted' => now()->format('H:i:s - d M Y'),
        ];

        $session->update([
            'violation_count' => 0,
            'is_disqualified' => false,
            'violation_logs' => $logs,
        ]);

        $studentName = $session->user?->name ?? 'Siswa';
        return redirect()->back()->with('success', "Peringatan pelanggaran untuk {$studentName} berhasil direset.");
    }

    /**
     * Client-side Anti-Cheat API endpoint: Log violation event from student exam screen.
     */
    public function logViolation(Request $request, ExamSession $session): JsonResponse
    {
        $validated = $request->validate([
            'type' => 'required|string|in:tab_switch,window_blur,mobile_minimize,copy_attempt,devtools_attempt',
            'message' => 'nullable|string',
            'duration_seconds' => 'nullable|integer',
        ]);

        $currentCount = $session->violation_count + 1;
        $logs = $session->violation_logs ?? [];

        $typeLabels = [
            'tab_switch' => 'Beralih Tab Browser / Membuka Tab Baru',
            'window_blur' => 'Layar Ujian Kehilangan Fokus / Membuka Aplikasi Lain',
            'mobile_minimize' => 'Aplikasi Browser Diminimalkan di HP',
            'copy_attempt' => 'Mencoba Menyalin (Copy) Teks Soal Ujian',
            'devtools_attempt' => 'Mencoba Membuka Inspect Element / DevTools',
        ];

        $logs[] = [
            'type' => $validated['type'],
            'title' => $typeLabels[$validated['type']] ?? 'Pelanggaran Layar Ujian',
            'message' => $validated['message'] ?? 'Terdeteksi meninggalkan layar pengerjaan ujian.',
            'duration_seconds' => $validated['duration_seconds'] ?? null,
            'timestamp' => now()->toIso8601String(),
            'time_formatted' => now()->format('H:i:s'),
        ];

        $isDisqualified = $currentCount >= 4 || $session->is_disqualified;

        $updates = [
            'violation_count' => $currentCount,
            'violation_logs' => $logs,
        ];

        // Otomatis diskualifikasi jika melebihi toleransi maksimal (misal >= 4x pelanggaran berat)
        if ($isDisqualified) {
            $updates['is_disqualified'] = true;
            $updates['is_passed'] = false;
        }

        $session->update($updates);

        return response()->json([
            'success' => true,
            'violation_count' => $currentCount,
            'is_disqualified' => $isDisqualified,
            'warning_message' => $currentCount >= 3 
                ? 'PERINGATAN KERAS! Anda telah melakukan 3x pelanggaran. Sistem pengawas Sensei telah mencatat data Anda!'
                : "PERINGATAN! Anda terdeteksi keluar dari layar ujian ({$currentCount}/3). Pelanggaran dicatat ke sistem Sensei.",
        ]);
    }
}
