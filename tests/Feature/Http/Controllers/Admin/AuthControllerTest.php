<?php

namespace Tests\Feature\Http\Controllers\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testItShouldLoginAValidCredentials()
    {
        $this->createPredictableAdminUser();

        $response = $this->postJson(route('api.admin.login'), [
            'email' => 'test@email.com',
            'password' => 'admin',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'token'
                ],
                'error',
                'errors',
            ]);

        $this->assertDatabaseCount('jwt_tokens', 1);
    }

    public function testItShouldNotLoginWithInvalidCredentials()
    {
        $response = $this->post(route('api.admin.login'), [
            'email' => 'test@email.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Failed to authenticate user',
            ]);
    }

    public function testLoginWithInvalidEmailAddress()
    {
        $response = $this->postJson(route('api.admin.login'), [
            'email' => 'testemail.com',
            'password' => 'admin',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors([
            'email' => trans('validation.email', ['attribute' => 'email']),
        ]);
    }

    public function testItShouldNotLoginIfUserIsNotAdmin()
    {
        $this->createPredictableAdminUser([
            'is_admin' => 0,
        ]);

        $response = $this->postJson(route('api.admin.login'), [
            'email' => 'test@email.com',
            'password' => 'admin',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'error' => 'Failed to authenticate user',
            ]);
    }

    public function testItSuccessfullyLogsOutAValidToken(): void
    {
        $user = $this->createPredictableAdminUser();
        Auth::login($user);

        $this->assertDatabaseCount('jwt_tokens', 1);

        $response = $this->getJson(route('api.admin.logout'));

        $response->assertStatus(200);
        $this->assertNull(Auth::user());
    }

    public function testItCannotAccessLogoutRouteIfAuthUserIsNotValid(): void
    {
        $response = $this->getJson(route('api.admin.logout'));

        $response->assertStatus(401)->assertJson([
            'error' => 'Unauthorized',
        ]);
    }
}
