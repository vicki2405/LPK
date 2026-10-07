<?php

namespace Tests\Feature;

use App\Models\Batch;
use App\Models\JobSector;
use App\Models\PipelineStage;
use App\Models\Program;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MasterStandaloneTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'siswa']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');
    }

    public function test_admin_can_create_batch_without_code_input(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.master.batches.store'), [
            'name' => 'Angkatan 25',
            'status' => 'active',
            'start_date' => '2026-10-01',
            'end_date' => '2027-04-01',
            'notes' => 'Batch baru tanpa form kode manual',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('batches', [
            'name' => 'Angkatan 25',
            'status' => 'active',
        ]);

        $batch = Batch::where('name', 'Angkatan 25')->first();
        $this->assertNotEmpty($batch->code);
    }

    public function test_admin_can_create_program_without_code_input(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.master.programs.store'), [
            'name' => 'Engineering Visa Jepang',
            'is_active' => true,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('programs', [
            'name' => 'Engineering Visa Jepang',
            'is_active' => true,
        ]);

        $program = Program::where('name', 'Engineering Visa Jepang')->first();
        $this->assertNotEmpty($program->code);
    }

    public function test_admin_can_create_job_sector_without_code_input(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.master.job-sectors.store'), [
            'name' => 'Pertanian Modern',
            'is_active' => true,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('job_sectors', [
            'name' => 'Pertanian Modern',
            'is_active' => true,
        ]);

        $sector = JobSector::where('name', 'Pertanian Modern')->first();
        $this->assertNotEmpty($sector->code);
    }

    public function test_admin_can_create_pipeline_stage_without_code_input(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.master.pipeline-stages.store'), [
            'name' => 'Pemberkasan Visa',
            'order_step' => 5,
            'badge_color' => 'blue',
            'is_active' => true,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('pipeline_stages', [
            'name' => 'Pemberkasan Visa',
            'order_step' => 5,
        ]);

        $stage = PipelineStage::where('name', 'Pemberkasan Visa')->first();
        $this->assertNotEmpty($stage->code);
    }

    public function test_admin_can_create_student_using_all_four_master_data(): void
    {
        $batch = Batch::create([
            'name' => 'Angkatan 10',
            'code' => 'BATCH_10',
            'status' => 'active',
        ]);

        $program = Program::create([
            'name' => 'Tokutei Ginou SSW',
            'code' => 'ssw_tokutei',
            'is_active' => true,
        ]);

        $sector = JobSector::create([
            'name' => 'Pengolahan Makanan',
            'code' => 'FOOD_PROCESSING',
            'is_active' => true,
        ]);

        $stage = PipelineStage::create([
            'name' => 'Pelatihan Pra-Keberangkatan',
            'code' => 'pap_training',
            'order_step' => 1,
            'badge_color' => 'blue',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.students.store'), [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@test.com',
            'nik' => '3201123456780001',
            'batch_id' => $batch->id,
            'program_type' => $program->name,
            'target_job_sector' => $sector->name,
            'target_language_level' => 'N4',
            'pipeline_stage' => $stage->code,
            'mcu_status' => 'fit',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'name' => 'Budi Santoso',
            'email' => 'budi.santoso@test.com',
            'program_type' => 'Tokutei Ginou SSW',
            'target_job_sector' => 'Pengolahan Makanan',
            'pipeline_stage' => 'pap_training',
        ]);

        $student = User::where('email', 'budi.santoso@test.com')->first();
        $this->assertTrue($student->batches()->where('batches.id', $batch->id)->exists());
    }
}
