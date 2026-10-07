<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleAndUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Buat Roles Dasar (Admin, Sensei, Siswa)
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);
        $roleSensei = Role::firstOrCreate(['name' => 'sensei']);
        $roleSiswa = Role::firstOrCreate(['name' => 'siswa']);

        // 2. Buat Akun Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin LPK',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->syncRoles([$roleAdmin]);

        // 3. Buat Akun Sensei (Yamada Sensei)
        $sensei = User::firstOrCreate(
            ['email' => 'sensei@gmail.com'],
            [
                'name' => 'Yamada Sensei',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $sensei->syncRoles([$roleSensei]);

        // 4. Buat Akun Siswa (Budi Siswa - N4 Trainee)
        $siswa = User::firstOrCreate(
            ['email' => 'siswa@gmail.com'],
            [
                'name' => 'Budi Siswa (N4 Trainee)',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $siswa->syncRoles([$roleSiswa]);
    }
}
