<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\QuestionOption;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CbtController extends Controller
{
    /**
     * Start or resume CBT Exam Session for authenticated student.
     */
    public function start(Exam $exam, Request $request): RedirectResponse
    {
        $user = $request->user();
        $isPrivileged = $user->hasAnyRole(['admin', 'sensei']);

        // 1. Keamanan: Validasi status publikasi ujian
        if (!$exam->is_published && !$isPrivileged) {
            return redirect()->route('siswa.exams.index')->with('error', 'Paket ujian belum diterbitkan oleh Sensei.');
        }

        // 2. Keamanan: Validasi jadwal mulai & berakhir ujian
        $now = now();
        if ($exam->start_time && $now->lessThan($exam->start_time) && !$isPrivileged) {
            return redirect()->route('siswa.exams.index')->with('error', 'Ujian belum dibuka. Jadwal mulai: ' . $exam->start_time->format('d M Y, H:i') . ' WIB.');
        }

        if ($exam->end_time && $now->greaterThan($exam->end_time) && !$isPrivileged) {
            return redirect()->route('siswa.exams.index')->with('error', 'Jadwal pengerjaan ujian ini telah berakhir pada ' . $exam->end_time->format('d M Y, H:i') . ' WIB.');
        }

        // 3. Keamanan: Validasi penugasan Angkatan (Batch)
        if ($exam->batch_id && !$isPrivileged) {
            $userBatchIds = $user->batches()->pluck('batches.id')->toArray();
            if (!in_array($exam->batch_id, $userBatchIds)) {
                return redirect()->route('siswa.exams.index')->with('error', 'Ujian ini hanya diperuntukkan bagi peserta dari angkatan/kelas khusus.');
            }
        }

        // 4. Cari sesi yang masih berjalan (in_progress)
        $session = ExamSession::where('user_id', $user->id)
            ->where('exam_id', $exam->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$session) {
            $previousSessions = ExamSession::where('user_id', $user->id)
                ->where('exam_id', $exam->id)
                ->get();

            // Cegah retake jika sebelumnya didiskualifikasi karena kecurangan
            $disqualifiedSession = $previousSessions->firstWhere('is_disqualified', true);
            if ($disqualifiedSession && !$isPrivileged) {
                return redirect()->route('siswa.cbt.result', $disqualifiedSession->id)
                    ->with('error', 'Anda telah didiskualifikasi dari ujian ini karena terdeteksi pelanggaran.');
            }

            // Batasi jumlah percobaan pengerjaan (max_attempts)
            $completedCount = $previousSessions->whereIn('status', ['submitted', 'completed'])->count();
            $maxAttempts = $exam->max_attempts ?: 1;
            if ($completedCount >= $maxAttempts && !$isPrivileged) {
                $lastSubmitted = $previousSessions->whereIn('status', ['submitted', 'completed'])->sortByDesc('id')->first();
                return redirect()->route('siswa.cbt.result', $lastSubmitted->id)
                    ->with('info', "Anda telah mencapai batas maksimal pengerjaan untuk ujian ini ({$maxAttempts}x).");
            }

            $durationMinutes = $exam->duration_minutes > 0 ? $exam->duration_minutes : 60;
            $session = ExamSession::create([
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'attempt_number' => $completedCount + 1,
                'started_at' => now(),
                'expires_at' => now()->addMinutes($durationMinutes),
                'status' => 'in_progress',
                'violation_count' => 0,
                'violation_logs' => [],
                'is_disqualified' => false,
            ]);
        }

        return redirect()->route('siswa.cbt.show', $session->id);
    }

    /**
     * Display full-screen interactive CBT Exam Engine with In-Memory Caching.
     */
    public function show(ExamSession $session): Response|RedirectResponse
    {
        $user = Auth::user();
        if ($session->user_id !== $user->id) {
            abort(403, 'Akses sesi ujian tidak diizinkan.');
        }

        if ($session->status === 'submitted') {
            return redirect()->route('siswa.cbt.result', $session->id);
        }

        $session->loadMissing('exam');

        // Fast In-Memory Cached questions bundle (100-1000 siswa dilayani dalam <2ms dari RAM)
        // KEAMANAN TINGKAT TINGGI: Kunci jawaban ('is_correct') TIDAK PERNAH dibocorkan ke client ujian siswa!
        $examId = $session->exam_id;
        $cacheKey = "cbt_exam_questions_{$examId}_secure";
        $questions = Cache::remember($cacheKey, 14400, function () use ($session) {
            return $session->exam->questions()
                ->with(['category:id,name,section_type', 'options:id,question_id,option_key,option_text,option_image'])
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
                        'category_name' => $q->category?->name ?? 'Tata Bahasa (Bunpou)',
                        'section_type' => $q->section_type ?? ($q->category?->section_type ?? 'bunpou'),
                        'instruction' => $q->instruction,
                        'question_text' => $q->question_text,
                        'audio_url' => $q->audio_url,
                        'audio_play_limit' => $q->audio_play_limit ?? 1,
                        'passage_text' => $q->reading_passage,
                        'image_url' => $q->image_url,
                        'explanation' => null, // Hidden during exam
                        'options' => $q->options->map(fn($opt) => [
                            'id' => $opt->id,
                            'option_key' => $opt->option_key,
                            'option_text' => $opt->option_text,
                            'option_image' => $opt->option_image,
                            'is_correct' => null, // Anti-cheat: strictly hidden from client
                        ])->values(),
                    ];
                })->values()->all();
        });

        // Hitung sisa detik
        $remainingSeconds = $session->expires_at ? max(0, now()->diffInSeconds($session->expires_at, false)) : 3600;

        // Load hanya jawaban siswa untuk sesi ini (targeted column selection)
        $existingAnswers = $session->answers()
            ->select('question_id', 'question_option_id', 'is_doubtful')
            ->get()
            ->mapWithKeys(fn($ans) => [
                $ans->question_id => [
                    'question_option_id' => $ans->question_option_id,
                    'is_doubtful' => (bool) $ans->is_doubtful,
                ]
            ]);

        $exam = $session->exam;

        $dbLeaderboard = ExamSession::where('exam_id', $session->exam_id)
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
                'isCurrentUser' => $s->user_id === $user->id,
            ])->values()->all();

        return Inertia::render('Siswa/Cbt/Show', [
            'session' => [
                'id' => $session->id,
                'status' => $session->status,
                'violation_count' => $session->violation_count,
                'is_disqualified' => $session->is_disqualified,
                'started_at' => $session->started_at,
                'expires_at' => $session->expires_at,
                'remaining_seconds' => $remainingSeconds,
            ],
            'exam' => [
                'id' => $exam->id,
                'title' => $exam->title,
                'code' => $exam->code,
                'level' => $exam->level,
                'duration_minutes' => $exam->duration_minutes,
                'passing_score' => $exam->passing_score,
                'max_score' => $exam->max_score,
                'display_mode' => $exam->display_mode ?? 'formal',
                'allow_student_mode_switch' => (bool) ($exam->allow_student_mode_switch ?? true),
            ],
            'questions' => $questions,
            'existingAnswers' => $existingAnswers,
            'initialLeaderboard' => $dbLeaderboard,
        ]);
    }

    /**
     * Ultra-fast atomic auto-save for single or batch answers (1 Atomic UPSERT Query).
     */
    public function saveAnswer(ExamSession $session, Request $request): JsonResponse
    {
        $user = Auth::user();
        if ($session->user_id !== $user->id || $session->status !== 'in_progress' || $session->is_disqualified) {
            return response()->json(['error' => 'Sesi ujian tidak valid, telah berakhir, atau Anda telah didiskualifikasi.'], 403);
        }

        // Keamanan: Validasi batas waktu pengerjaan ujian (toleransi 2 menit untuk latensi jaringan)
        if ($session->expires_at && now()->greaterThan($session->expires_at->addMinutes(2))) {
            return response()->json(['error' => 'Waktu pengerjaan ujian telah berakhir.'], 403);
        }

        // Mendukung single object atau batch array
        $answers = $request->input('answers');
        if (!is_array($answers)) {
            $validated = $request->validate([
                'question_id' => 'required|integer',
                'question_option_id' => 'nullable|integer',
                'is_doubtful' => 'nullable|boolean',
            ]);
            $answers = [$validated];
        }

        $now = now();
        $records = [];
        foreach ($answers as $ans) {
            if (empty($ans['question_id'])) continue;
            $records[] = [
                'exam_session_id' => $session->id,
                'question_id' => (int) $ans['question_id'],
                'question_option_id' => !empty($ans['question_option_id']) ? (int) $ans['question_option_id'] : null,
                'is_doubtful' => !empty($ans['is_doubtful']) ? 1 : 0,
                'is_correct' => 0, // Dihitung presisi saat submit
                'score_earned' => 0.00,
                'answered_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        $latestOptionId = null;
        $latestQuestionId = null;
        if (!empty($records)) {
            $latest = end($records);
            $latestOptionId = $latest['question_option_id'] ?? null;
            $latestQuestionId = $latest['question_id'] ?? null;

            // Transaksi dengan 3x auto-retry untuk menangani InnoDB deadlock pada konkurensi tinggi
            DB::transaction(function () use ($records) {
                DB::table('exam_answers')->upsert(
                    $records,
                    ['exam_session_id', 'question_id'],
                    ['question_option_id', 'is_doubtful', 'answered_at', 'updated_at']
                );
            }, 3);
        }

        // Anti-Cheat Ketat: Kunci jawaban ('is_correct' & 'correct_option_id') HANYA boleh dikirim jika ujian murni bertipe game arcade!
        // Pada simulasi resmi N4 / ujian formal, kunci jawaban DILARANG KERAS dibocorkan ke client dalam respon apa pun.
        $isGameMode = false;
        $isCorrect = null;
        $correctOptionId = null;
        $liveLeaderboard = null;

        $exam = $session->exam;
        $isOfficialExam = in_array($exam->exam_type ?? '', ['jlpt_simulation', 'jft_simulation']) || (($exam->display_mode ?? 'formal') !== 'game');

        if (!$isOfficialExam && $latestOptionId) {
            $isGameMode = true;
            $isCorrect = (bool) DB::table('question_options')
                ->where('id', $latestOptionId)
                ->value('is_correct');
            $correctOptionId = DB::table('question_options')
                ->where('question_id', $latestQuestionId)
                ->where('is_correct', 1)
                ->value('id');

            // Optimasi performa: Cache leaderboard selama 5 detik agar tidak memicu query filesort di setiap jawaban siswa
            $cacheKeyLb = "live_leaderboard_{$session->exam_id}";
            $rawLeaderboard = Cache::remember($cacheKeyLb, 5, function () use ($session) {
                return ExamSession::where('exam_id', $session->exam_id)
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
                    ])->values()->all();
            });

            $liveLeaderboard = array_map(function ($item) use ($user) {
                $item['isCurrentUser'] = ($item['user_id'] === $user->id);
                return $item;
            }, $rawLeaderboard);
        }

        return response()->json([
            'success' => true, 
            'synced_count' => count($records),
            'is_correct' => $isCorrect,
            'correct_option_id' => $correctOptionId,
            'question_id' => $latestQuestionId,
            'live_leaderboard' => $liveLeaderboard,
        ]);
    }

    /**
     * Client-side Anti-Cheat API endpoint: Log violation event from student exam screen.
     */
    public function logViolation(Request $request, ExamSession $session): JsonResponse
    {
        $user = Auth::user();
        if ($session->user_id !== $user->id || $session->status !== 'in_progress') {
            return response()->json(['error' => 'Sesi tidak valid'], 403);
        }

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

        // Diskualifikasi tegas pada pelanggaran ke-3 atau lebih
        $isDisqualified = $currentCount >= 3 || $session->is_disqualified;

        $updates = [
            'violation_count' => $currentCount,
            'violation_logs' => $logs,
        ];

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
                ? 'PERINGATAN KERAS! Anda telah melakukan 3x pelanggaran. Anda telah didiskualifikasi dari ujian ini!'
                : "PERINGATAN! Anda terdeteksi keluar dari layar ujian ({$currentCount}/3). Pelanggaran dicatat ke sistem Sensei.",
        ]);
    }

    /**
     * Submit and score the CBT exam session according to N4 standards (Batch Scoring).
     */
    public function submit(ExamSession $session, Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($session->user_id !== $user->id) {
            abort(403);
        }

        if ($session->status === 'submitted') {
            return redirect()->route('siswa.cbt.result', $session->id);
        }

        // Keamanan: Validasi waktu kedaluwarsa sesi (jika lewat > 5 menit dari batas waktu, abaikan jawaban pending baru)
        if ($session->expires_at && now()->greaterThan($session->expires_at->addMinutes(5))) {
            $request->merge(['pending_answers' => []]);
        }

        // 1. Simpan jawaban pending terakhir dari client jika disertakan
        $pendingAnswers = $request->input('pending_answers');
        if (is_array($pendingAnswers) && !empty($pendingAnswers)) {
            $now = now();
            $records = [];
            foreach ($pendingAnswers as $ans) {
                if (empty($ans['question_id'])) continue;
                $records[] = [
                    'exam_session_id' => $session->id,
                    'question_id' => (int) $ans['question_id'],
                    'question_option_id' => !empty($ans['question_option_id']) ? (int) $ans['question_option_id'] : null,
                    'is_doubtful' => !empty($ans['is_doubtful']) ? 1 : 0,
                    'is_correct' => 0,
                    'score_earned' => 0.00,
                    'answered_at' => $now,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
            if (!empty($records)) {
                DB::table('exam_answers')->upsert(
                    $records,
                    ['exam_session_id', 'question_id'],
                    ['question_option_id', 'is_doubtful', 'answered_at', 'updated_at']
                );
            }
        }

        // 2. Load pertanyaan ujian dengan opsi benar & kategori untuk koreksi massal
        $questions = $session->exam->questions()
            ->with(['options:id,question_id,is_correct', 'category:id,name,section_type'])
            ->get();

        $totalQuestionsCount = $questions->count();
        $answers = $session->answers()->get()->keyBy('question_id');

        $correctCount = 0;
        $answeredCount = 0;
        $mojiGoiScore = 0.0;
        $bunpouDokkaiScore = 0.0;
        $choukaiScore = 0.0;
        $rawScore = 0.0;
        $now = now();

        $answerUpdates = [];

        foreach ($questions as $question) {
            $userAns = $answers->get($question->id);
            $selectedOptId = $userAns?->question_option_id;

            if ($selectedOptId) {
                $answeredCount++;
                // Cek apakah opsi yang dipilih adalah kunci jawaban benar
                $correctOpt = $question->options->firstWhere('is_correct', true);
                $isCorrect = $correctOpt && ($correctOpt->id == $selectedOptId);
                $points = $isCorrect ? (float) ($question->score_points ?? 1.0) : 0.0;

                if ($isCorrect) {
                    $correctCount++;
                    $rawScore += $points;

                    $secType = $question->section_type ?? ($question->category?->section_type ?? 'bunpou');
                    $catName = strtolower($question->category?->name ?? '');

                    if ($secType === 'moji_goi' || str_contains($catName, 'moji') || str_contains($catName, 'goi') || str_contains($catName, 'kosakata') || str_contains($catName, 'kanji')) {
                        $mojiGoiScore += $points;
                    } elseif ($secType === 'choukai' || str_contains($catName, 'choukai') || str_contains($catName, 'listening') || str_contains($catName, 'pendengaran')) {
                        $choukaiScore += $points;
                    } else {
                        $bunpouDokkaiScore += $points;
                    }
                }

                $answerUpdates[] = [
                    'exam_session_id' => $session->id,
                    'question_id' => $question->id,
                    'question_option_id' => $selectedOptId,
                    'is_doubtful' => $userAns->is_doubtful ? 1 : 0,
                    'is_correct' => $isCorrect ? 1 : 0,
                    'score_earned' => $points,
                    'answered_at' => $userAns->answered_at ?? $now,
                    'created_at' => $userAns->created_at ?? $now,
                    'updated_at' => $now,
                ];
            }
        }

        // Batch update penilaian jawaban dalam 1 transaksi
        if (!empty($answerUpdates)) {
            DB::table('exam_answers')->upsert(
                $answerUpdates,
                ['exam_session_id', 'question_id'],
                ['is_correct', 'score_earned', 'updated_at']
            );
        }

        $unansweredCount = max(0, $totalQuestionsCount - $answeredCount);
        $wrongCount = max(0, $answeredCount - $correctCount);

        // Skalakan skor jika bobot pertanyaan kecil ke skala standar 180 (Standar Resmi JLPT N4)
        $maxRawScore = $questions->sum('score_points') ?: 1;
        $maxExamScore = $session->exam->max_score ?: 180;
        $scaleFactor = $maxExamScore / $maxRawScore;

        $scaledTotalScore = round(($rawScore / $maxRawScore) * $maxExamScore, 1);
        $scaledMojiGoi = round($mojiGoiScore * $scaleFactor, 1);
        $scaledBunpouDokkai = round($bunpouDokkaiScore * $scaleFactor, 1);
        $scaledChoukai = round($choukaiScore * $scaleFactor, 1);

        $passingScore = $session->exam->passing_score ?: 90;
        $isPassed = $scaledTotalScore >= $passingScore && !$session->is_disqualified;

        $session->update([
            'status' => 'submitted',
            'submitted_at' => now(),
            'total_score' => $scaledTotalScore,
            'moji_goi_score' => $scaledMojiGoi,
            'bunpou_dokkai_score' => $scaledBunpouDokkai,
            'choukai_score' => $scaledChoukai,
            'correct_answers_count' => $correctCount,
            'wrong_answers_count' => $wrongCount,
            'unanswered_count' => $unansweredCount,
            'is_passed' => $isPassed,
        ]);

        return redirect()->route('siswa.cbt.result', $session->id);
    }

    /**
     * Display CBT Exam completion result and feedback.
     */
    public function result(ExamSession $session): Response
    {
        $user = Auth::user();
        if ($session->user_id !== $user->id && !$user->hasAnyRole(['admin', 'sensei'])) {
            abort(403, 'Akses hasil ujian tidak diizinkan.');
        }

        $session->load('exam');

        return Inertia::render('Siswa/Cbt/Result', [
            'session' => $session,
            'exam' => $session->exam,
        ]);
    }
}
