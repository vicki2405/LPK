<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $roleName = 'siswa';
        $roleNames = [];
        $permissionNames = [];

        if ($user) {
            $sessionKey = 'auth_meta_' . $user->id;
            $cachedMeta = $request->session()->get($sessionKey);

            if (!$cachedMeta) {
                $user->loadMissing('roles.permissions');
                $roleNames = $user->roles->pluck('name')->toArray();
                if (in_array('admin', $roleNames)) {
                    $roleName = 'admin';
                } elseif (in_array('sensei', $roleNames)) {
                    $roleName = 'sensei';
                } elseif (in_array('siswa', $roleNames)) {
                    $roleName = 'siswa';
                }
                $permissionNames = $user->roles->flatMap->permissions->pluck('name')->unique()->values()->toArray();

                $cachedMeta = [
                    'role' => $roleName,
                    'roles' => $roleNames,
                    'permissions' => $permissionNames,
                ];
                $request->session()->put($sessionKey, $cachedMeta);
            } else {
                $roleName = $cachedMeta['role'];
                $roleNames = $cachedMeta['roles'];
                $permissionNames = $cachedMeta['permissions'];
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'avatar' => $user->avatar,
                    'avatar_url' => $user->avatar_url,
                    'role' => $roleName,
                    'roles' => $roleNames,
                    'permissions' => $permissionNames,
                ] : null,
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ];
    }
}
