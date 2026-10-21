<?php

namespace App\Http\Controllers\Sensei;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\ExamSession;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class GradeController extends Controller
{
    /**
     * Display student gradebook and CBT progress.
     */
    public function index(Request $request): Response
    {
        $query = User::role('siswa')
            ->with([
                'batches',
                'examSessions' => fn($q) => $q->latest()->with('exam:id,title,max_score')
            ])
            ->withCount('examSessions');

        if ($request->filled('batch_id')) {
            $query->whereHas('batches', function ($q) use ($request) {
                $q->where('batches.id', $request->batch_id);
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(12)->withQueryString();
        $batches = Batch::where('status', 'active')->select('id', 'name', 'code')->get();
        $recentSessions = ExamSession::with(['user', 'exam'])
            ->whereIn('status', ['submitted', 'completed'])
            ->latest()
            ->take(10)
            ->get();

        return Inertia::render('Sensei/Grades/Index', [
            'students' => $students,
            'batches' => $batches,
            'recentSessions' => $recentSessions,
            'filters' => $request->only(['batch_id', 'search']),
        ]);
    }

    /**
     * Display student detailed grade card.
     */
    public function show(User $user): Response
    {
        $user->load([
            'batches:id,name,code',
            'examSessions' => fn($q) => $q->latest()->take(15)->with([
                'exam:id,title,level,max_score,passing_score',
                'answers' => fn($qa) => $qa->select('id', 'exam_session_id', 'question_id', 'question_option_id', 'is_doubtful', 'is_correct', 'score_earned')->with([
                    'question' => fn($qq) => $qq->select('id', 'question_category_id', 'chapter_id', 'level', 'section_type', 'question_text', 'score_points', 'explanation')->with([
                        'category:id,name,code,level,section_type',
                        'chapter:id,chapter_number,title',
                        'options:id,question_id,option_key,option_text,is_correct',
                    ]),
                    'selectedOption:id,question_id,option_key,option_text,is_correct',
                ]),
            ]),
        ]);

        return Inertia::render('Sensei/Grades/Show', [
            'student' => $user,
        ]);
    }

    /**
     * Delete a single CBT exam session from student grade history.
     */
    public function destroySession(ExamSession $session): RedirectResponse
    {
        $examTitle = $session->exam?->title ?? 'Ujian CBT';
        $studentName = $session->user?->name ?? 'Siswa';

        // Delete associated answers
        $session->answers()->delete();
        $session->delete();

        return redirect()->back()->with('success', "Riwayat sesi {$examTitle} untuk {$studentName} berhasil dihapus.");
    }

    /**
     * Reset all exam sessions and scores for a student.
     */
    public function resetStudentGrades(User $user): RedirectResponse
    {
        $sessions = ExamSession::where('user_id', $user->id)->get();
        foreach ($sessions as $session) {
            $session->answers()->delete();
            $session->delete();
        }

        return redirect()->back()->with('success', "Seluruh riwayat ujian untuk {$user->name} berhasil dibersihkan.");
    }
}
