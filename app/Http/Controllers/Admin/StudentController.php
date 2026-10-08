<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Batch;
use App\Models\JobSector;
use App\Models\LanguageLevel;
use App\Models\PipelineStage;
use App\Models\Program;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;

class StudentController extends Controller
{
    /**
     * Display a listing of students.
     */
    public function index(Request $request): Response
    {
        $query = User::role('siswa')
            ->with('batches')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('nik', 'like', "%{$search}%");
            });
        }

        if ($request->filled('batch_id')) {
            $query->whereHas('batches', function ($q) use ($request) {
                $q->where('batches.id', $request->batch_id);
            });
        }

        if ($request->filled('program_type')) {
            $query->where('program_type', $request->program_type);
        }

        if ($request->filled('target_job_sector')) {
            $query->where('target_job_sector', $request->target_job_sector);
        }

        if ($request->filled('pipeline_stage')) {
            $query->where('pipeline_stage', $request->pipeline_stage);
        }

        $students = $query->paginate(10)->withQueryString();
        $batches = Batch::where('status', 'active')->select('id', 'name', 'code')->orderBy('id', 'desc')->get();
        $programs = Program::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $jobSectors = JobSector::where('is_active', true)->orderBy('sort_order', 'asc')->get();
        $pipelineStages = PipelineStage::where('is_active', true)->orderBy('order_step', 'asc')->get();
        $languageLevels = LanguageLevel::where('is_active', true)->orderBy('sort_order', 'asc')->get();

        return Inertia::render('Admin/Students/Index', [
            'students' => $students,
            'batches' => $batches,
            'programs' => $programs,
            'jobSectors' => $jobSectors,
            'pipelineStages' => $pipelineStages,
            'languageLevels' => $languageLevels,
            'filters' => $request->only(['search', 'batch_id', 'program_type', 'target_job_sector', 'pipeline_stage']),
        ]);
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'nik' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'batch_id' => 'required|exists:batches,id',
            'program_type' => 'required|string|max:100',
            'target_job_sector' => 'required|string|max:100',
            'target_language_level' => 'required|string|max:50',
            'pipeline_stage' => 'required|string|max:50',
            'mcu_status' => 'required|in:pending,fit,unfit',
            'passport_number' => 'nullable|string|max:50',
            'password' => 'nullable|string|min:6',
        ]);

        $password = !empty($validated['password']) ? Hash::make($validated['password']) : Hash::make('password');

        $student = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'nik' => $validated['nik'] ?? null,
            'phone' => $validated['phone'] ?? null,
            'gender' => $validated['gender'] ?? null,
            'birth_place' => $validated['birth_place'] ?? null,
            'birth_date' => $validated['birth_date'] ?? null,
            'program_type' => $validated['program_type'],
            'target_job_sector' => $validated['target_job_sector'],
            'target_language_level' => $validated['target_language_level'],
            'pipeline_stage' => $validated['pipeline_stage'],
            'mcu_status' => $validated['mcu_status'],
            'passport_number' => $validated['passport_number'] ?? null,
            'password' => $password,
        ]);

        $student->assignRole('siswa');

        if (!empty($validated['batch_id'])) {
            $student->batches()->attach($validated['batch_id'], ['role_in_batch' => 'siswa']);
        }

        return redirect()->back()->with('success', "Siswa {$student->name} berhasil didaftarkan!");
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, User $student): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $student->id,
            'nik' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:20',
            'gender' => 'nullable|in:L,P',
            'birth_place' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date',
            'batch_id' => 'required|exists:batches,id',
            'program_type' => 'required|string|max:100',
            'target_job_sector' => 'required|string|max:100',
            'target_language_level' => 'required|string|max:50',
            'pipeline_stage' => 'required|string|max:50',
            'mcu_status' => 'required|in:pending,fit,unfit',
            'coe_status' => 'required|in:pending,submitted,issued,rejected',
            'visa_status' => 'required|in:pending,issued',
            'passport_number' => 'nullable|string|max:50',
        ]);

        $student->update($validated);

        if (isset($validated['batch_id'])) {
            $student->batches()->sync([$validated['batch_id'] => ['role_in_batch' => 'siswa']]);
        }

        return redirect()->back()->with('success', "Data siswa {$student->name} berhasil diperbarui!");
    }

    /**
     * Reset student password to default ('password' or custom).
     */
    public function resetPassword(Request $request, User $student): RedirectResponse
    {
        $newPassword = $request->input('password', 'password');
        $student->update(['password' => Hash::make($newPassword)]);

        return redirect()->back()->with('success', "Kata sandi siswa {$student->name} berhasil direset!");
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(User $student): RedirectResponse
    {
        $student->delete();
        return redirect()->back()->with('success', 'Data siswa berhasil dihapus!');
    }
}
