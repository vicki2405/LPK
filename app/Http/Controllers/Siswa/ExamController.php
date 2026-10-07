<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExamController extends Controller
{
    /**
     * Display full CBT Exam Portal for Siswa.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $batch = $user->batches()->first();

        // Ujian aktif yang ditugaskan untuk batch siswa (Optimized: hanya ambil metadata & questions_count tanpa load ribuan objek butir soal)
        $assignedExams = Exam::where('is_published', true)
            ->where(function ($q) use ($batch) {
                $q->whereNull('batch_id')
                  ->orWhere('batch_id', $batch?->id);
            })
            ->with(['creator:id,name', 'subject:id,name,code'])
            ->withCount('questions')
            ->latest()
            ->get();

        // Riwayat sesi ujian siswa
        $recentSessions = ExamSession::where('user_id', $user->id)
            ->with('exam:id,title,code,level,max_score,passing_score')
            ->latest()
            ->take(20)
            ->get();

        $completedSessions = $recentSessions->filter(fn($s) => in_array($s->status, ['submitted', 'completed']));
        $passedCount = $completedSessions->where('is_passed', true)->count();
        $avgScore = $completedSessions->count() > 0 ? round($completedSessions->avg('total_score'), 1) : 0;

        return Inertia::render('Siswa/Exams/Index', [
            'batch' => $batch,
            'assignedExams' => $assignedExams,
            'recentSessions' => $recentSessions,
            'stats' => [
                'total_assigned' => $assignedExams->count(),
                'total_taken' => $completedSessions->count(),
                'total_passed' => $passedCount,
                'avg_score' => $avgScore,
            ],
        ]);
    }
}
