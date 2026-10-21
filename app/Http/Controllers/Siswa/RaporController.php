<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RaporController extends Controller
{
    /**
     * Display student's comprehensive academic report card, CBT progress, and competency indicators.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();

        $user->load([
            'batches:id,name,code',
            'examSessions' => fn($q) => $q->whereIn('status', ['submitted', 'completed'])
                ->latest()
                ->with([
                    'exam:id,title,level,max_score,passing_score',
                    'answers' => fn($qa) => $qa->select(
                        'id',
                        'exam_session_id',
                        'question_id',
                        'question_option_id',
                        'is_doubtful',
                        'is_correct',
                        'score_earned'
                    )->with([
                        'question' => fn($qq) => $qq->select(
                            'id',
                            'question_category_id',
                            'chapter_id',
                            'level',
                            'section_type',
                            'question_text',
                            'score_points',
                            'explanation'
                        )->with([
                            'category:id,name,code,level,section_type',
                            'chapter:id,chapter_number,title',
                            'options:id,question_id,option_key,option_text,is_correct',
                        ]),
                        'selectedOption:id,question_id,option_key,option_text,is_correct',
                    ]),
                ]),
        ]);

        return Inertia::render('Siswa/Rapor/Index', [
            'student' => $user,
        ]);
    }
}
