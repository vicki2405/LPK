<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_forgot_password_screen_cannot_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(404);
    }

    public function test_forgot_password_cannot_be_requested(): void
    {
        $response = $this->post('/forgot-password', ['email' => 'test@example.com']);

        $response->assertStatus(404);
    }

    public function test_reset_password_screen_cannot_be_rendered(): void
    {
        $response = $this->get('/reset-password/sample-token');

        $response->assertStatus(404);
    }

    public function test_reset_password_cannot_be_posted(): void
    {
        $response = $this->post('/reset-password', [
            'token' => 'sample-token',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(404);
    }
}
