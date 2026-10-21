<?php

namespace App\Http\Controllers\Sensei;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\QuestionBank;
use App\Models\QuestionCategory;
use App\Models\QuestionLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class QuestionSettingsController extends Controller
{
    // ========================================================================
    // QUESTION LEVELS CRUD
    // ========================================================================

    /**
     * Halaman Pengaturan Bank Soal: Level & Kategori dalam satu halaman.
     */
    public function index(): Response
    {
        $levels = QuestionLevel::withCount(['questions'])
            ->orderBy('order_index')
            ->get();

        $categories = QuestionCategory::withCount(['questions'])
            ->orderBy('section_type')
            ->orderBy('name')
            ->get();

        return Inertia::render('Sensei/Settings/QuestionSettings', [
            'levels'     => $levels,
            'categories' => $categories,
        ]);
    }

    // --- LEVELS ---

    public function storeLevel(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'        => 'nullable|string|max:20',
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'order_index' => 'nullable|integer|min:0',
        ], [
            'name.required' => 'Nama level wajib diisi.',
        ]);

        $code = !empty($validated['code'])
            ? strtoupper(trim($validated['code']))
            : strtoupper(preg_replace('/[^A-Za-z0-9_]/', '', str_replace([' ', '-'], '_', trim($validated['name']))));

        if (empty($code)) {
            $code = 'LV_' . time();
        }

        // Pastikan kode unik
        $baseCode = substr($code, 0, 16);
        $candidateCode = $baseCode;
        $counter = 1;
        while (QuestionLevel::where('code', $candidateCode)->exists()) {
            $candidateCode = substr($baseCode, 0, 14) . '_' . $counter++;
        }

        QuestionLevel::create([
            'code'        => $candidateCode,
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order_index' => $validated['order_index'] ?? 99,
            'is_active'   => true,
        ]);

        return back()->with('success', "Level '{$validated['name']}' berhasil ditambahkan.");
    }

    public function updateLevel(Request $request, QuestionLevel $level): RedirectResponse
    {
        $validated = $request->validate([
            'code'        => "nullable|string|max:20|unique:question_levels,code,{$level->id}",
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string',
            'order_index' => 'nullable|integer|min:0',
            'is_active'   => 'boolean',
        ], [
            'name.required' => 'Nama level wajib diisi.',
        ]);

        $oldCode = $level->code;
        $newCode = !empty($validated['code']) ? strtoupper(trim($validated['code'])) : $oldCode;

        $level->update([
            'code'        => $newCode,
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'order_index' => $validated['order_index'] ?? $level->order_index,
            'is_active'   => $validated['is_active'] ?? $level->is_active,
        ]);

        // Jika kode level berubah, sinkronkan level_code pada questions dan question_categories
        if ($oldCode !== $newCode) {
            Question::where('level_code', $oldCode)->update(['level_code' => $newCode]);
            QuestionCategory::where('level_code', $oldCode)->update(['level_code' => $newCode]);
        }

        return back()->with('success', "Level '{$level->name}' berhasil diperbarui.");
    }

    public function destroyLevel(Request $request, QuestionLevel $level): RedirectResponse
    {
        $questionsCount = Question::where('level_code', $level->code)
            ->orWhere('level', $level->code)
            ->count();

        $categoriesCount = QuestionCategory::where('level_code', $level->code)
            ->orWhere('level', $level->code)
            ->count();

        $banksCount = QuestionBank::where('level', $level->code)->count();

        if ($questionsCount > 0 || $categoriesCount > 0 || $banksCount > 0) {
            $details = [];
            if ($questionsCount > 0) $details[] = "{$questionsCount} butir soal";
            if ($categoriesCount > 0) $details[] = "{$categoriesCount} kategori/indikator";
            if ($banksCount > 0) $details[] = "{$banksCount} paket bank soal";

            return back()->withErrors([
                'error' => "Level '{$level->name}' ({$level->code}) tidak dapat dihapus karena masih digunakan oleh: " . implode(', ', $details) . ". Silakan ubah atau hapus data terkait terlebih dahulu."
            ]);
        }

        $level->delete();
        return back()->with('success', "Level '{$level->name}' berhasil dihapus.");
    }

    // --- CATEGORIES ---

    public function storeCategory(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'nullable|string|max:30',
            'section_type' => 'required|in:moji_goi,bunpou,dokkai,choukai',
            'level_code'   => 'nullable|string|max:20',
            'description'  => 'nullable|string',
        ], [
            'name.required'         => 'Nama kategori/indikator wajib diisi.',
            'section_type.required' => 'Tipe seksi wajib dipilih.',
        ]);

        $levelCode = !empty($validated['level_code']) ? strtoupper(trim($validated['level_code'])) : null;
        $legacyLevel = in_array($levelCode, ['N5', 'N4', 'N3', 'JFT_A2']) ? $levelCode : 'N4';

        QuestionCategory::create([
            'name'         => $validated['name'],
            'code'         => !empty($validated['code']) ? strtoupper(trim($validated['code'])) : null,
            'section_type' => $validated['section_type'],
            'level_code'   => $levelCode,
            'level'        => $legacyLevel,
            'description'  => $validated['description'] ?? null,
        ]);

        return back()->with('success', "Kategori '{$validated['name']}' berhasil ditambahkan.");
    }

    public function updateCategory(Request $request, QuestionCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:100',
            'code'         => 'nullable|string|max:30',
            'section_type' => 'required|in:moji_goi,bunpou,dokkai,choukai',
            'level_code'   => 'nullable|string|max:20',
            'description'  => 'nullable|string',
        ], [
            'name.required'         => 'Nama kategori/indikator wajib diisi.',
            'section_type.required' => 'Tipe seksi wajib dipilih.',
        ]);

        $levelCode = !empty($validated['level_code']) ? strtoupper(trim($validated['level_code'])) : null;
        $legacyLevel = in_array($levelCode, ['N5', 'N4', 'N3', 'JFT_A2']) ? $levelCode : ($category->level ?? 'N4');

        $category->update([
            'name'         => $validated['name'],
            'code'         => array_key_exists('code', $validated) ? (!empty($validated['code']) ? strtoupper(trim($validated['code'])) : null) : $category->code,
            'section_type' => $validated['section_type'],
            'level_code'   => $levelCode,
            'level'        => $legacyLevel,
            'description'  => $validated['description'] ?? null,
        ]);

        return back()->with('success', "Kategori '{$category->name}' berhasil diperbarui.");
    }

    public function destroyCategory(Request $request, QuestionCategory $category): RedirectResponse
    {
        $questionsCount = $category->questions()->count();
        if ($questionsCount > 0) {
            if ($request->boolean('force_detach')) {
                // Lepaskan relasi soal dari kategori ini (set null)
                $category->questions()->update(['question_category_id' => null]);
            } else {
                return back()->withErrors([
                    'error' => "Kategori '{$category->name}' tidak dapat dihapus karena masih digunakan oleh {$questionsCount} butir soal. Anda dapat melepaskan relasi soal atau menghapus soal terkait terlebih dahulu."
                ]);
            }
        }

        $category->delete();
        return back()->with('success', "Kategori '{$category->name}' berhasil dihapus.");
    }
}
