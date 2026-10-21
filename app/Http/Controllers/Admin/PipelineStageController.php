<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PipelineStage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PipelineStageController extends Controller
{
    public function index(): Response
    {
        $stages = PipelineStage::orderBy('order_step', 'asc')->get();

        return Inertia::render('Admin/Master/PipelineStages/Index', [
            'stages' => $stages,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_jp' => 'nullable|string|max:255',
            'order_step' => 'required|integer',
            'badge_color' => 'required|string|max:30',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $baseCode = strtolower(\Illuminate\Support\Str::slug($validated['name'], '_')) ?: 'stage';
        $code = $baseCode;
        $counter = 1;
        while (PipelineStage::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter++;
        }
        $validated['code'] = $code;

        PipelineStage::create($validated);

        return redirect()->back()->with('success', "Tahapan Penyaluran {$validated['name']} berhasil ditambahkan!");
    }

    public function update(Request $request, PipelineStage $pipelineStage): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_jp' => 'nullable|string|max:255',
            'order_step' => 'required|integer',
            'badge_color' => 'required|string|max:30',
            'icon' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $pipelineStage->update($validated);

        return redirect()->back()->with('success', "Tahapan Penyaluran {$pipelineStage->name} berhasil diperbarui!");
    }

    public function destroy(PipelineStage $pipelineStage): RedirectResponse
    {
        $usedCount = \App\Models\User::where('pipeline_stage', $pipelineStage->name)
            ->orWhere('pipeline_stage', $pipelineStage->code)
            ->count();

        if ($usedCount > 0) {
            return redirect()->back()->with('error', "Tahapan Penyaluran '{$pipelineStage->name}' masih digunakan oleh {$usedCount} siswa. Ubah tahapan siswa terlebih dahulu sebelum menghapus.");
        }

        $pipelineStage->delete();
        return redirect()->back()->with('success', 'Tahapan Penyaluran berhasil dihapus!');
    }
}
