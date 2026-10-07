<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Standard Permissions for LPK System
        $permissions = [
            // User & Security Management
            ['name' => 'manage_users', 'guard_name' => 'web'],
            ['name' => 'manage_roles', 'guard_name' => 'web'],

            // Trainee & Class Management
            ['name' => 'manage_students', 'guard_name' => 'web'],
            ['name' => 'manage_batches', 'guard_name' => 'web'],
            ['name' => 'manage_pipeline', 'guard_name' => 'web'],

            // CBT & Curriculum Management
            ['name' => 'manage_cbt_exams', 'guard_name' => 'web'],
            ['name' => 'manage_questions', 'guard_name' => 'web'],
            ['name' => 'manage_courses', 'guard_name' => 'web'],
            ['name' => 'grade_exams', 'guard_name' => 'web'],

            // Student Exam Access
            ['name' => 'take_exams', 'guard_name' => 'web'],
            ['name' => 'view_learning_materials', 'guard_name' => 'web'],

            // Master Data & Reports
            ['name' => 'manage_master_data', 'guard_name' => 'web'],
            ['name' => 'view_analytics', 'guard_name' => 'web'],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(['name' => $perm['name'], 'guard_name' => 'web'], $perm);
        }

        // Assign default permissions to Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions(Permission::all());

        $senseiRole = Role::firstOrCreate(['name' => 'sensei', 'guard_name' => 'web']);
        $senseiRole->syncPermissions([
            'manage_students',
            'manage_cbt_exams',
            'manage_questions',
            'manage_courses',
            'grade_exams',
            'view_analytics',
        ]);

        $siswaRole = Role::firstOrCreate(['name' => 'siswa', 'guard_name' => 'web']);
        $siswaRole->syncPermissions([
            'take_exams',
            'view_learning_materials',
        ]);
    }
}
