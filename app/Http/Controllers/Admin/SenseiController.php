<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class SenseiController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::role('sensei')
            ->with('batches')
            ->withCount(['batches', 'createdCourses', 'createdExams'])
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('katakana_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $senseis = $query->paginate(10)->withQueryString();
        $batches = Batch::where('status', 'active')->select('id', 'name', 'code')->get();

        return Inertia::render('Admin/Senseis/Index', [
            'senseis' => $senseis,
            'batches' => $batches,
            'filters' => $request->only(['search']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'katakana_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:6',
            'batch_ids' => 'nullable|array',
            'batch_ids.*' => 'exists:batches,id',
        ]);

        $password = !empty($validated['password']) ? Hash::make($validated['password']) : Hash::make('password');

        $sensei = User::create([
            'name' => $validated['name'],
            'katakana_name' => $validated['katakana_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $password,
        ]);

        $sensei->assignRole('sensei');

        if (!empty($validated['batch_ids'])) {
            $syncData = [];
            foreach ($validated['batch_ids'] as $batchId) {
                $syncData[$batchId] = ['role_in_batch' => 'sensei'];
            }
            $sensei->batches()->sync($syncData);
        }

        return redirect()->back()->with('success', "Sensei {$sensei->name} berhasil ditambahkan!");
    }

    public function update(Request $request, User $sensei): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'katakana_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $sensei->id,
            'phone' => 'nullable|string|max:20',
            'batch_ids' => 'nullable|array',
            'batch_ids.*' => 'exists:batches,id',
        ]);

        $sensei->update([
            'name' => $validated['name'],
            'katakana_name' => $validated['katakana_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        if ($request->has('batch_ids')) {
            $syncData = [];
            foreach ($validated['batch_ids'] ?? [] as $batchId) {
                $syncData[$batchId] = ['role_in_batch' => 'sensei'];
            }
            $sensei->batches()->sync($syncData);
        }

        return redirect()->back()->with('success', "Data Sensei {$sensei->name} berhasil diperbarui!");
    }

    public function resetPassword(Request $request, User $sensei): RedirectResponse
    {
        $newPassword = $request->input('password', 'password');
        $sensei->update(['password' => Hash::make($newPassword)]);

        return redirect()->back()->with('success', "Password Sensei {$sensei->name} berhasil direset!");
    }

    public function destroy(User $sensei): RedirectResponse
    {
        $hasExams = $sensei->createdExams()->count();
        if ($hasExams > 0) {
            return redirect()->back()->with('error', "Sensei {$sensei->name} masih memiliki {$hasExams} ujian CBT yang dibuat. Alihkan kepemilikan ujian sebelum menghapus.");
        }

        $sensei->batches()->detach();
        $sensei->delete();
        return redirect()->back()->with('success', 'Data Sensei berhasil dihapus!');
    }
}
