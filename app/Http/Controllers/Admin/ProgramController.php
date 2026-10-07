<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    public function index(): Response
    {
        $programs = Program::orderBy('sort_order', 'asc')->get();

        return Inertia::render('Admin/Master/Programs/Index', [
            'programs' => $programs,
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

        $baseCode = strtolower(\Illuminate\Support\Str::slug($validated['name'], '_')) ?: 'prog';
        $code = $baseCode;
        $counter = 1;
        while (Program::where('code', $code)->exists()) {
            $code = $baseCode . '_' . $counter++;
        }
        $validated['code'] = $code;

        Program::create($validated);

        return redirect()->back()->with('success', "Program {$validated['name']} berhasil ditambahkan!");
    }

    public function update(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'name_jp' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'sort_order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        $program->update($validated);

        return redirect()->back()->with('success', "Program {$program->name} berhasil diperbarui!");
    }

    public function destroy(Program $program): RedirectResponse
    {
        $usedInUsers = \App\Models\User::where('program_type', $program->name)
            ->orWhere('program_type', $program->code)
            ->count();

        $usedInBatches = \App\Models\Batch::where('program_type', $program->code)
            ->orWhere('program_type', $program->name)
            ->count();

        if ($usedInUsers > 0 || $usedInBatches > 0) {
            return redirect()->back()->with('error', "Program '{$program->name}' masih digunakan ({$usedInUsers} siswa, {$usedInBatches} angkatan). Pindahkan data terkait terlebih dahulu sebelum menghapus.");
        }

        $program->delete();
        return redirect()->back()->with('success', 'Program berhasil dihapus!');
    }
}
