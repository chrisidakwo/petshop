<?php

namespace Tests\Feature\Http\Controllers\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItResetsPassword(): void
    {
        $this->createPredictableRegularUser([
            'email' => 'test@email.com',
        ]);

        $token = Password::sendResetLink([
            'email' => 'test@email.com',
        ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);

        $response = $this->postJson(route('api.user.reset-password-token'), [
            'token' => $token,
            'email' => 'test@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'message' => 'Password has been successfully updated',
                ],
            ]);

        $this->assertDatabaseCount('password_reset_tokens', 0);
    }

    public function testItDoesNotValidateOnAnInvalidToken(): void
    {
        $this->createPredictableRegularUser([
            'email' => 'test@email.com',
        ]);

        $token = Password::sendResetLink([
            'email' => 'test@email.com',
        ]);

        $response = $this->postJson(route('api.user.reset-password-token'), [
            'token' => "{$token}ekrjtkre3",
            'email' => 'test@email.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Invalid or expired token'
            ]);
    }
}
