<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseBackupRestoreTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $siswa;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'admin']);
        Role::firstOrCreate(['name' => 'sensei']);
        Role::firstOrCreate(['name' => 'siswa']);

        $this->admin = User::factory()->create();
        $this->admin->assignRole('admin');

        $this->siswa = User::factory()->create();
        $this->siswa->assignRole('siswa');
    }

    public function test_admin_can_access_database_management_page(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.database.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Admin/Database/Index')
            ->has('databaseStats')
        );
    }

    public function test_non_admin_cannot_access_database_management_page(): void
    {
        $response = $this->actingAs($this->siswa)->get(route('admin.database.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_download_database_sql_backup(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.database.download'));

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'application/sql; charset=utf-8');
        $this->assertTrue(str_contains($response->headers->get('Content-Disposition') ?? '', 'backup_lms_lpk_'));
    }

    public function test_restore_requires_file_and_exact_confirmation(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.database.restore'), [
            'backup_file' => null,
            'confirmation' => 'SALAH',
        ]);

        $response->assertSessionHasErrors(['backup_file', 'confirmation']);
    }

    public function test_restore_rejects_non_sql_file(): void
    {
        $file = UploadedFile::fake()->create('malicious.php', 10, 'text/php');

        $response = $this->actingAs($this->admin)->post(route('admin.database.restore'), [
            'backup_file' => $file,
            'confirmation' => 'RESTORE',
        ]);

        $response->assertSessionHas('error');
    }

    public function test_backup_downloaded_file_can_be_restored_successfully(): void
    {
        // 1. Download backup as admin
        $downloadResponse = $this->actingAs($this->admin)->get(route('admin.database.download'));
        $downloadResponse->assertStatus(200);

        // Capture streamed SQL content
        ob_start();
        $downloadResponse->sendContent();
        $sqlContent = ob_get_clean();

        $this->assertNotEmpty($sqlContent);
        $this->assertStringContainsString('SET FOREIGN_KEY_CHECKS=0;', $sqlContent);
        $this->assertStringContainsString('users', $sqlContent);

        // 2. Bungkus ke berkas UploadedFile
        $tempPath = tempnam(sys_get_temp_dir(), 'test_sql_');
        file_put_contents($tempPath, $sqlContent);
        $uploadedFile = new UploadedFile($tempPath, 'backup_test.sql', 'application/sql', null, true);

        // 3. Kirim ke endpoint restore
        $restoreResponse = $this->actingAs($this->admin)->post(route('admin.database.restore'), [
            'backup_file' => $uploadedFile,
            'confirmation' => 'RESTORE',
        ]);

        @unlink($tempPath);

        $restoreResponse->assertRedirect();
        $restoreResponse->assertSessionHas('success');
        $this->assertDatabaseHas('users', [
            'email' => $this->admin->email,
        ]);
    }
}

