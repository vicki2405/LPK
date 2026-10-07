<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JobSector;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class JobSectorController extends Controller
{
    public function index(): Response
    {
        $sectors = JobSector::orderBy('sort_order', 'asc')->latest()->get();

        return Inertia::render('Admin/Master/JobSectors/Index', [
            'sectors' => $sectors,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_jp' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $baseCode = strtoupper(\Illuminate\Support\Str::slug($validated['name'], '_')) ?: 'SECTOR';
        $code = $baseCode;
        $counter = 1;
        while (JobSector::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter++;
        }
        $validated['code'] = $code;

        JobSector::create($validated);

        return redirect()->back()->with('success', "Bidang Kerja {$validated['name']} berhasil ditambahkan!");
    }

    public function update(Request $request, JobSector $jobSector): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_jp' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $jobSector->update($validated);

        return redirect()->back()->with('success', "Bidang Kerja {$jobSector->name} berhasil diperbarui!");
    }

    public function destroy(JobSector $jobSector): RedirectResponse
    {
        $usedCount = \App\Models\User::where('target_job_sector', $jobSector->name)
            ->orWhere('target_job_sector', $jobSector->code)
            ->count();

        if ($usedCount > 0) {
            return redirect()->back()->with('error', "Bidang Kerja '{$jobSector->name}' masih digunakan oleh {$usedCount} siswa. Ubah bidang kerja siswa terlebih dahulu sebelum menghapus.");
        }

        $jobSector->delete();
        return redirect()->back()->with('success', 'Bidang Kerja berhasil dihapus!');
    }
}
