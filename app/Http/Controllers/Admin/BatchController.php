<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BatchController extends Controller
{
    /**
     * Display a listing of batches in Master Data.
     */
    public function index(): Response
    {
        $batches = Batch::withCount('siswas')
            ->latest()
            ->get();

        return Inertia::render('Admin/Master/Batches/Index', [
            'batches' => $batches,
        ]);
    }

    /**
     * Store a newly created batch in Master Data.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,graduated,archived',
            'notes' => 'nullable|string',
        ]);

        $baseCode = strtoupper(\Illuminate\Support\Str::slug($validated['name'], ''));
        if (empty($baseCode)) {
            $baseCode = 'BATCH';
        }
        $code = $baseCode;
        $counter = 1;
        while (Batch::where('code', $code)->exists()) {
            $code = $baseCode . '-' . $counter;
            $counter++;
        }

        $validated['code'] = $code;
        Batch::create($validated);

        return redirect()->back()->with('success', 'Master Angkatan baru berhasil ditambahkan!');
    }

    /**
     * Update the specified batch in Master Data.
     */
    public function update(Request $request, Batch $batch): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'required|in:active,graduated,archived',
            'notes' => 'nullable|string',
        ]);

        $batch->update($validated);

        return redirect()->back()->with('success', 'Data Master Angkatan berhasil diperbarui!');
    }

    /**
     * Remove the specified batch from storage.
     */
    public function destroy(Batch $batch): RedirectResponse
    {
        $siswasCount = $batch->siswas()->count();
        if ($siswasCount > 0) {
            return redirect()->back()->with('error', "Angkatan '{$batch->name}' masih memiliki {$siswasCount} siswa terdaftar. Pindahkan atau lepaskan siswa terlebih dahulu sebelum menghapus angkatan.");
        }

        $batch->delete();
        return redirect()->back()->with('success', 'Data Master Angkatan berhasil dihapus!');
    }
}
