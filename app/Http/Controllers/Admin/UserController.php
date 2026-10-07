<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of admin/staff users (excluding students and senseis).
     */
    public function index(Request $request): Response
    {
        // Hanya tampilkan akun Administrator / Staf Manajemen (mengecualikan siswa & sensei)
        $query = User::whereDoesntHave('roles', function ($q) {
            $q->whereIn('name', ['siswa', 'sensei']);
        })->with('roles')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('katakana_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $role = $request->role;
            $query->whereHas('roles', function ($q) use ($role) {
                $q->where('name', $role);
            });
        }

        $users = $query->paginate(10)->withQueryString();
        $roles = Role::whereNotIn('name', ['siswa', 'sensei'])->select('id', 'name')->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles,
            'filters' => $request->only(['search', 'role']),
        ]);
    }

    /**
     * Store a newly created user with role assignment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'katakana_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name|not_in:siswa,sensei',
            'password' => 'nullable|string|min:6',
        ], [
            'role.not_in' => 'Akun Siswa dan Sensei dikelola secara terpisah melalui menu manajemen masing-masing.',
        ]);

        $password = !empty($validated['password']) ? Hash::make($validated['password']) : Hash::make('password');

        $user = User::create([
            'name' => $validated['name'],
            'katakana_name' => $validated['katakana_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => $password,
        ]);

        $user->assignRole($validated['role']);

        return redirect()->back()->with('success', "Akun {$user->name} berhasil dibuat dengan peran {$validated['role']}!");
    }

    /**
     * Update user details and role.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'katakana_name' => 'nullable|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'role' => 'required|exists:roles,name|not_in:siswa,sensei',
        ], [
            'role.not_in' => 'Akun Siswa dan Sensei dikelola secara terpisah melalui menu manajemen masing-masing.',
        ]);

        $user->update([
            'name' => $validated['name'],
            'katakana_name' => $validated['katakana_name'] ?? null,
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        $user->syncRoles([$validated['role']]);

        return redirect()->back()->with('success', "Data akun {$user->name} berhasil diperbarui!");
    }

    /**
     * Reset password for user.
     */
    public function resetPassword(Request $request, User $user): RedirectResponse
    {
        $newPassword = $request->input('password', 'password');
        $user->update(['password' => Hash::make($newPassword)]);

        return redirect()->back()->with('success', "Password akun {$user->name} berhasil direset!");
    }

    /**
     * Remove the user from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        if (Auth::id() === $user->id) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->hasAnyRole(['siswa', 'sensei'])) {
            return redirect()->back()->with('error', 'Akun siswa dan sensei harus dikelola dan dihapus melalui menu masing-masing.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'Akun pengguna berhasil dihapus!');
    }
}
