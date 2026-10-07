<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\PipelineStage;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JourneyController extends Controller
{
    /**
     * Display student's Japan Career & Visa Pipeline stages.
     */
    public function index(Request $request): Response
    {
        $user = $request->user();
        $batch = $user->batches()->first();

        $pipelineStages = PipelineStage::where('is_active', true)
            ->orderBy('order_step', 'asc')
            ->get();

        return Inertia::render('Siswa/Journey/Index', [
            'user' => $user,
            'batch' => $batch,
            'pipelineStages' => $pipelineStages,
        ]);
    }
}
