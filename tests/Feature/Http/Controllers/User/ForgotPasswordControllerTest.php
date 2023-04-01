<?php

declare(strict_types=1);

namespace Tests\Feature\Http\Controllers\User;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItSendsPasswordResetNotification(): void
    {
        Notification::fake();

        $user = $this->createPredictableRegularUser([
            'email' => 'user@email.com',
        ]);

        $response = $this->postJson(route('api.user.forgot-password'), [
            'email' => 'user@email.com',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'reset_token',
                ]
            ]);

        $this->assertDatabaseCount('password_reset_tokens', 1);

        Notification::assertSentTo($user, ResetPassword::class);
    }
}
