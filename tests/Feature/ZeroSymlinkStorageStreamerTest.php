<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ZeroSymlinkStorageStreamerTest extends TestCase
{
    public function test_storage_streamer_serves_existing_file_with_correct_mime_and_cache_headers(): void
    {
        $testFileName = 'unit_test_dummy_audio.mp3';
        $testDir = public_path('storage/test_symlink');
        $testFilePath = $testDir . '/' . $testFileName;

        if (!File::exists($testDir)) {
            File::makeDirectory($testDir, 0755, true);
        }

        File::put($testFilePath, 'dummy mp3 content');

        try {
            $response = $this->get('/storage/test_symlink/' . $testFileName);

            $response->assertStatus(200);
            $response->assertHeader('Content-Type', 'audio/mpeg');
            $response->assertHeader('Cache-Control', 'immutable, max-age=31536000, public');
            $response->assertHeader('Access-Control-Allow-Origin', '*');
        } finally {
            if (File::exists($testDir)) {
                File::deleteDirectory($testDir);
            }
        }
    }

    public function test_storage_streamer_returns_404_for_non_existent_file(): void
    {
        $response = $this->get('/storage/non_existent_random_file_xyz_123.jpg');
        $response->assertStatus(404);
    }

    public function test_storage_streamer_sanitizes_directory_traversal(): void
    {
        $response = $this->get('/storage/../../../../etc/passwd');
        $response->assertStatus(404);
    }
}

