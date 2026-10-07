<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\Chapter;
use App\Models\Course;
use App\Models\Exam;
use App\Models\ExamAnswer;
use App\Models\ExamSession;
use App\Models\PipelineStage;
use App\Models\Question;
use App\Models\QuestionCategory;
use App\Models\User;
use App\Models\Vocabulary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    /**
     * Dispatch user to role-specific dashboard.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('sensei')) {
            return redirect()->route('sensei.dashboard');
        }

        return redirect()->route('siswa.dashboard');
    }

    /**
     * Admin Operational & Analytics Dashboard.
     */
    /**
     * Admin Operational & Analytics Dashboard.
     */
    public function adminDashboard(): Response
    {
        $stats = Cache::remember('admin_dashboard_stats', 30, function () {
            $totalSiswa = User::role('siswa')->count();
            $totalSensei = User::role('sensei')->count();
            $totalBatches = Batch::where('status', 'active')->count();
            $activeExams = Exam::where('is_published', true)->count();

            // Hitung Tingkat Kelulusan Ujian N4 Siswa Riil via Direct SQL Count
            $completedSessionsCount = ExamSession::whereIn('status', ['submitted', 'completed'])->count();
            if ($completedSessionsCount > 0) {
                $passedCount = ExamSession::whereIn('status', ['submitted', 'completed'])->where('is_passed', true)->count();
                $passingRate = (int) round(($passedCount / $completedSessionsCount) * 100);
            } else {
                $passingRate = 92; // Baseline indikator kelulusan LPK
            }

            // Jumlah siswa siap terbang / visa terbit
            $readyToFly = User::role('siswa')
                ->where(function ($q) {
                    $q->whereIn('pipeline_stage', ['visa', 'terbang'])
                      ->orWhere('visa_status', 'issued');
                })
                ->count();

            return [
                'total_siswa' => $totalSiswa,
                'total_sensei' => $totalSensei,
                'total_batches' => $totalBatches,
                'active_exams' => $activeExams,
                'passing_rate' => $passingRate,
                'ready_to_fly' => $readyToFly,
            ];
        });

        $pipelineData = Cache::remember('admin_dashboard_pipeline', 30, function () {
            // Agregasi Funnel Pipeline 7 Tahapan Penyaluran Siswa via single query
            $pipelineCounts = User::role('siswa')
                ->selectRaw('pipeline_stage, count(*) as count')
                ->groupBy('pipeline_stage')
                ->pluck('count', 'pipeline_stage')
                ->toArray();

            return [
                'pelatihan' => $pipelineCounts['pelatihan'] ?? 0,
                'lulus_n4' => $pipelineCounts['lulus_n4'] ?? 0,
                'matching_mensetsu' => $pipelineCounts['matching'] ?? 0,
                'proses_mcu' => $pipelineCounts['mcu'] ?? 0,
                'proses_coe' => $pipelineCounts['coe'] ?? 0,
                'visa_ready' => $pipelineCounts['visa'] ?? 0,
                'terbang' => $pipelineCounts['terbang'] ?? 0,
            ];
        });

        $recentStudents = User::role('siswa')->with('batches')->latest()->take(6)->get();
        $batches = Batch::where('status', 'active')->withCount('siswas')->latest()->take(6)->get();

        return Inertia::render('Admin/Dashboard', [
            'stats' => $stats,
            'pipeline' => $pipelineData,
            'batches' => $batches,
            'recentStudents' => $recentStudents,
        ]);
    }

    /**
     * Sensei Command Center Dashboard.
     */
    public function senseiDashboard(): Response
    {
        $totalQuestions = Question::count();
        $activeExams = Exam::where('is_published', true)->latest()->get();
        $totalCourses = Course::where('is_published', true)->count();
        $totalChapters = Chapter::where('is_published', true)->count();
        $batches = Batch::where('status', 'active')
            ->withCount(['siswas', 'exams'])
            ->get();

        // Agregasi Heatmap Kelemahan Siswa Riil dari Database secara efisien (Direct SQL Group By)
        $categoryStats = \Illuminate\Support\Facades\DB::table('exam_answers')
            ->join('questions', 'exam_answers.question_id', '=', 'questions.id')
            ->leftJoin('question_categories', 'questions.question_category_id', '=', 'question_categories.id')
            ->selectRaw("
                COALESCE(question_categories.name, 'Tata Bahasa (Bunpou)') as topic,
                COUNT(exam_answers.id) as total_answered,
                SUM(CASE WHEN exam_answers.is_correct = 0 THEN 1 ELSE 0 END) as wrong_count
            ")
            ->groupBy('topic')
            ->get();

        $weaknessHeatmap = [];
        foreach ($categoryStats as $row) {
            $totalInCat = (int) $row->total_answered;
            $wrongCount = (int) $row->wrong_count;
            if ($totalInCat > 0) {
                $wrongPercentage = (int) round(($wrongCount / $totalInCat) * 100);
                $status = $wrongPercentage >= 50 ? 'critical' : ($wrongPercentage >= 25 ? 'warning' : 'good');

                $weaknessHeatmap[] = [
                    'topic' => $row->topic,
                    'wrong_percentage' => $wrongPercentage,
                    'wrong_count' => $wrongCount,
                    'total_answered' => $totalInCat,
                    'status' => $status,
                ];
            }
        }

        if (!empty($weaknessHeatmap)) {
            usort($weaknessHeatmap, fn($a, $b) => $b['wrong_percentage'] <=> $a['wrong_percentage']);
        }

        $dynamicAdvice = null;
        if (!empty($weaknessHeatmap)) {
            $worstTopic = $weaknessHeatmap[0];
            if ($worstTopic['wrong_percentage'] > 0) {
                $dynamicAdvice = "Tingkat kesalahan pada materi 「{$worstTopic['topic']}」 mencapai {$worstTopic['wrong_percentage']}%. Disarankan mengadakan sesi penguatan materi sebelum jadwal ujian berikutnya.";
            }
        }

        // Siswa yang terdeteksi melakukan pelanggaran ujian CBT
        $violationSessions = ExamSession::with(['user.batches', 'exam'])
            ->where(function($q) {
                $q->where('violation_count', '>', 0)
                  ->orWhere('is_disqualified', true);
            })
            ->latest()
            ->take(5)
            ->get();

        $totalStudents = $batches->sum('siswas_count') > 0 ? $batches->sum('siswas_count') : User::role('siswa')->count();

        return Inertia::render('Sensei/Dashboard', [
            'stats' => [
                'total_questions' => $totalQuestions,
                'active_exams' => $activeExams->count(),
                'total_courses' => $totalCourses,
                'total_chapters' => $totalChapters,
                'total_students_guided' => $totalStudents,
                'total_violations' => ExamSession::where('violation_count', '>', 0)->count(),
            ],
            'batches' => $batches,
            'activeExams' => $activeExams,
            'weaknessHeatmap' => $weaknessHeatmap,
            'dynamicAdvice' => $dynamicAdvice,
            'violationSessions' => $violationSessions,
        ]);
    }

    /**
     * Siswa Interactive Training Camp & CBT Dashboard.
     */
    public function siswaDashboard(Request $request): Response
    {
        $user = $request->user();

        // Cari batch siswa
        $batch = $user->batches()->first();

        // Ujian CBT yang ditugaskan & aktif dari Sensei (Optimized: questions_count tanpa load ribuan model soal)
        $assignedExams = Exam::where('is_published', true)
            ->where(function ($q) use ($batch) {
                $q->whereNull('batch_id')
                  ->orWhere('batch_id', $batch?->id);
            })
            ->with(['creator:id,name', 'subject:id,name,code'])
            ->withCount('questions')
            ->latest()
            ->get();

        // Bab terakhir yang dipelajari (ambil yang diterbitkan Sensei)
        $currentChapter = Chapter::with(['course', 'vocabularies'])
            ->where('is_published', true)
            ->orderBy('chapter_number', 'asc')
            ->first();

        // Sampel Kosakata Hari Ini untuk Flashcards (dioptimalkan di memori tanpa query ORDER BY RAND)
        $todayVocabularies = Cache::remember('today_vocabularies_sample', 3600, function () {
            $pool = Vocabulary::with('chapter')
                ->whereHas('chapter', fn($q) => $q->where('is_published', true))
                ->take(40)
                ->get();

            if ($pool->isEmpty()) {
                $pool = Vocabulary::take(15)->get();
            }

            return $pool;
        })->shuffle()->take(12)->values();

        // Riwayat Sesi Ujian Siswa (dibatasi 10 sesi terakhir untuk efisiensi render)
        $recentSessions = ExamSession::where('user_id', $user->id)
            ->with('exam')
            ->latest()
            ->take(10)
            ->get();

        // Hitung Kesiapan Ujian N4 secara riil dari riwayat nilai siswa
        $completedSessions = $recentSessions->filter(fn($s) => in_array($s->status, ['submitted', 'completed']));
        $passedSessions = $completedSessions->where('is_passed', true);
        
        if ($completedSessions->count() > 0) {
            $avgScore = $completedSessions->avg('total_score') ?? 0;
            $scoreRate = ($avgScore / 180) * 100;
            $passBonus = ($passedSessions->count() / $completedSessions->count()) * 20;
            $readinessPercentage = (int) min(100, max(20, round(($scoreRate * 0.8) + $passBonus)));
        } else {
            $readinessPercentage = 45; // Baseline awal
        }

        $readinessStatus = $readinessPercentage >= 80 
            ? 'Sangat Siap Lulus N4' 
            : ($readinessPercentage >= 60 ? 'Siap Uji Kelulusan' : 'Perlu Penguatan Kosakata & Tata Bahasa');

        // Master Tahap Penyaluran (Pipeline Stages)
        $pipelineStages = PipelineStage::where('is_active', true)->orderBy('order_step')->get();

        return Inertia::render('Siswa/Dashboard', [
            'readiness' => [
                'percentage' => $readinessPercentage,
                'status_label' => $readinessStatus,
                'target_level' => 'JLPT N4 & JFT-Basic A2',
                'target_date' => 'Desember 2026',
            ],
            'batch' => $batch,
            'currentChapter' => $currentChapter,
            'assignedExams' => $assignedExams,
            'todayVocabularies' => $todayVocabularies,
            'recentSessions' => $recentSessions,
            'pipelineStages' => $pipelineStages,
        ]);
    }
}
