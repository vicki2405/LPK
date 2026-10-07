<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            MasterDataSeeder::class,
            RoleAndUserSeeder::class,
            LmsAndCbtSampleSeeder::class,
            LmsCurriculumTenChaptersSeeder::class,
            WebsiteSettingsSeeder::class,
            CbtSampleExamSeeder::class,
            LearningIndicatorsRealSeeder::class,
        ]);
    }
}
