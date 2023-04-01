<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Auth\Jwt;
use App\Auth\Providers\JwtProvider;
use App\Http\Parsers\AuthHeader;
use App\Http\Services\JwtTokenService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    protected Jwt $jwt;
    protected JwtTokenService $jwtTokenService;

    protected function setUp(): void
    {
        parent::setUp();

        $provider = new JwtProvider(
            $this->getTestSecretKey(),
            [
                'private' => $this->getTestPrivateKey(),
                'public' => $this->getTestPublicKey(),
            ]
        );

        $this->app->bind(\App\Auth\Contracts\Providers\JWT::class, fn () => $provider);

        $this->jwt = new Jwt(
            resolve(Request::class),
            [new AuthHeader()],
            $provider,
        );

        $this->jwtTokenService = resolve(JwtTokenService::class);
    }

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

    public function testItDoesNotLoginARegularUser(): void
    {
        $this->createPredictableRegularUser([
            'email' => 'user@email.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->postJson(route('api.admin.login'), [
            'email' => 'user@email.com',
            'password' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJson([
                'success' => 0,
                'error' => 'Failed to authenticate user',
            ]);
    }

    public function testItSuccessfullyLogsOutWhenTokenIsPresentInRequestHeader(): void
    {
        $user = $this->createPredictableAdminUser();

        $token = $this->jwt->generateTokenFromUser($user);
        $this->jwtTokenService->create($token,$user);

        $response = $this->getJson(route('api.admin.logout'), [
            'Authorization' => "Bearer $token",
        ]);

        $response->assertStatus(200);
        $this->assertNull(Auth::user());
        $this->assertDatabaseCount('jwt_tokens', 0);
    }

    public function testItCannotAccessLogoutRouteIfAuthUserIsNotValid(): void
    {
        $response = $this->getJson(route('api.admin.logout'));

        $response->assertStatus(401)->assertJson([
            'error' => 'Unauthorized',
        ]);
    }
}
