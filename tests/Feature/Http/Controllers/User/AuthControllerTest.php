<?php

namespace Tests\Feature\Http\Controllers\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItLogsInAUser(): void
    {
        $this->createPredictableRegularUser([
            'email' => 'user@email.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson(route('api.user.login'), [
            'email' => 'user@email.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token'
                ],
            ]);

        $response->assertJson([
            'success' => 1,
        ]);
    }

    public function testItShouldNotLoginWithInvalidCredential()
    {
        $this->createPredictableRegularUser();

        $response = $this->postJson(route('api.user.login'), [
            'email' => 'user@email.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => 0,
                'error' => 'Failed to authenticate user',
            ]);
    }
}
