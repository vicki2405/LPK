<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Chapter;
use App\Models\QuestionCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class LearningIndicatorController extends Controller
{
    /**
     * Display a listing of Learning Outcome Indicators (Indikator Capaian Pembelajaran).
     */
    public function index(Request $request): Response
    {
        $query = QuestionCategory::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $indicators = $query->orderBy('name', 'asc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => QuestionCategory::count(),
        ];

        return Inertia::render('Admin/Master/LearningIndicators/Index', [
            'indicators' => $indicators,
            'stats' => $stats,
            'filters' => $request->only(['search']),
        ]);
    }

    /**
     * Store a newly created Learning Outcome Indicator in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:question_categories,name',
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama indikator capaian pembelajaran wajib diisi.',
            'name.unique' => 'Nama indikator ini sudah terdaftar sebelumnya.',
            'name.max' => 'Nama indikator maksimal 255 karakter.',
        ]);

        QuestionCategory::create([
            'name' => trim($validated['name']),
            'description' => $validated['description'] ?? null,
            'section_type' => 'moji_goi',
            'level' => 'N4',
            'level_code' => 'N4',
            'code' => null,
            'chapter_id' => null,
        ]);

        return redirect()->back()->with('success', 'Indikator capaian pembelajaran berhasil ditambahkan.');
    }

    /**
     * Update the specified Learning Outcome Indicator in storage.
     */
    public function update(Request $request, QuestionCategory $learningIndicator): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:question_categories,name,' . $learningIndicator->id,
            'description' => 'nullable|string',
        ], [
            'name.required' => 'Nama indikator capaian pembelajaran wajib diisi.',
            'name.unique' => 'Nama indikator ini sudah terdaftar sebelumnya.',
            'name.max' => 'Nama indikator maksimal 255 karakter.',
        ]);

        $learningIndicator->update([
            'name' => trim($validated['name']),
            'description' => $validated['description'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Indikator capaian pembelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified Learning Outcome Indicator from storage.
     */
    public function destroy(QuestionCategory $learningIndicator): RedirectResponse
    {
        $questionsCount = $learningIndicator->questions()->count();

        if ($questionsCount > 0) {
            return redirect()->back()->with('error', "Indikator ini sedang digunakan oleh {$questionsCount} butir soal CBT. Lepaskan soal terlebih dahulu sebelum menghapus.");
        }

        $learningIndicator->delete();

        return redirect()->back()->with('success', 'Indikator capaian pembelajaran berhasil dihapus.');
    }
}
