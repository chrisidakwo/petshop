<?php

namespace Tests\Unit\Auth;

use App\Auth\Jwt;
use App\Auth\JwtGuard;
use App\Events\UserLoggedIn;
use App\Http\Services\JwtTokenService;
use App\Models\JwtToken;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Event;
use Mockery;
use Tests\Models\TestUser1;
use Tests\TestCase;

class JwtGuardTest extends TestCase
{
    protected Jwt $jwt;
    protected JwtGuard $guard;
    protected UserProvider $provider;
    protected JwtTokenService $jwtTokenService;

    protected function setUp(): void
    {
        parent::setUp();

        $this->jwt = Mockery::mock(Jwt::class);
        $this->provider = Mockery::mock(EloquentUserProvider::class);
        $this->jwtTokenService = Mockery::mock(JwtTokenService::class);

        $this->guard = new JwtGuard(
            $this->provider,
            Request::create('/test-url'),
            $this->jwt,
            $this->jwtTokenService,
        );
    }

    public function testIsShouldReturnNullIfNoTokenIsProvided(): void
    {
        $this->jwt->expects('parseRequestForToken')->times(2)->andReturn(null);
        $this->jwt->expects('validateToken')->never();
        $this->jwt->expects('validateSubject')->never();

        $this->assertNull($this->guard->user());
        $this->assertFalse($this->guard->check());
    }

    public function testItShouldReturnNullIfAnInvalidTokenIsProvided(): void
    {
        $this->jwt->shouldReceive('parseRequestForToken')
            ->times(2)->andReturn('is.invalid.token');

        $this->jwt->shouldReceive('validateToken')->twice()->andReturn(false);

        $this->assertNull($this->guard->user());
        $this->assertFalse($this->guard->check());
    }

    public function testIsShouldReturnFalseIfProvidedCredentialsDoNotPass(): void
    {
        $credentials = [
            'email' => 'test@email.com',
            'password' => 'password',
        ];

        $user = new TestUser1;

        $this->provider->shouldReceive('retrieveByCredentials')
            ->times(2)
            ->with($credentials)
            ->andReturn($user);

        $this->provider->shouldReceive('validateCredentials')
            ->times(2)
            ->with($user, $credentials)
            ->andReturn(false);

        $this->assertFalse($this->guard->attempt($credentials, false));
        $this->assertFalse($this->guard->validate($credentials));
    }

    public function testItShouldReturnTrueIfProvidedCredentialsPass()
    {
        $credentials = [
            'email' => 'test@email.com',
            'password' => 'password',
        ];

        $user = new TestUser1;

        $this->provider->shouldReceive('retrieveByCredentials')
            ->times(2)
            ->with($credentials)
            ->andReturn($user);

        $this->provider->shouldReceive('validateCredentials')
            ->times(2)
            ->with($user, $credentials)
            ->andReturn(true);

        $this->assertTrue($this->guard->attempt($credentials, false));
        $this->assertTrue($this->guard->validate($credentials));
    }

    public function testIsShouldReturnTokenIfProvidedCredentialsPassAndUserIsFound(): void
    {
        $credentials = [
            'email' => 'test@email.com',
            'password' => 'password',
        ];

        $user = new TestUser1();

        $this->provider->shouldReceive('retrieveByCredentials')
            ->once()
            ->with($credentials)
            ->andReturn($user);

        $this->provider->shouldReceive('validateCredentials')
            ->once()
            ->with($user, $credentials)
            ->andReturn(true);

        $this->jwt->shouldReceive('generateTokenFromUser')
            ->once()
            ->with($user)
            ->andReturn('is.valid.token');

        $this->jwt->shouldReceive('setToken')
            ->once()
            ->with('is.valid.token')
            ->andReturnSelf();

        Event::fake();

        $token = $this->guard->attempt($credentials);

        $this->assertSame('is.valid.token', $token);
        $this->assertSame($user, $this->guard->getLastAttempted());

        Event::assertDispatchedTimes(UserLoggedIn::class);
    }

    public function testItShouldGetAuthenticatedUserIfAValidTokenIsProvided(): void
    {
        $this->jwt->shouldReceive('parseRequestForToken')->once()->andReturn('is.valid.token');
        $this->jwt->shouldReceive('validateToken')->once()->andReturn([
            'sub' => '3',
        ]);

        $this->provider->shouldReceive('getModel')
            ->once()
            ->andReturn(TestUser1::class);

        $this->jwt->shouldReceive('checkSubjectModel')
            ->once()
            ->with(TestUser1::class)
            ->andReturn(true);

        $this->jwtTokenService->shouldReceive('find')
            ->once()
            ->andReturn(new JwtToken());

        $this->provider->shouldReceive('retrieveById')
            ->once()
            ->with(3)
            ->andReturn(new TestUser1(['id' => 3]));

        $this->assertSame(3, $this->guard->user()->id);
        $this->assertTrue($this->guard->check());
    }

    public function testItShouldReturnNullIfTheTokenIsExpiredOrDoesNotExistInTokensTable()
    {
        $this->jwt->shouldReceive('parseRequestForToken')->twice()->andReturn('is.valid.token');
        $this->jwt->shouldReceive('validateToken')->twice()->andReturn([
            'sub' => '1',
        ]);

        $this->provider->shouldReceive('getModel')
            ->twice()
            ->andReturn(TestUser1::class);

        $this->jwt->shouldReceive('checkSubjectModel')
            ->twice()
            ->with(TestUser1::class)
            ->andReturn(true);

        $this->jwtTokenService->shouldReceive('find')
            ->twice()
            ->andReturn(null);

        $this->provider->shouldReceive('retrieveById')
            ->never();

        $this->assertNull($this->guard->user());
        $this->assertFalse($this->guard->check());
    }

    public function testItShouldLogoutATokenAndDeleteFromDB(): void
    {
        $this->jwt->shouldReceive('setToken')
            ->once()
            ->with('is.valid.token')
            ->andReturnSelf();

        $this->jwtTokenService->shouldReceive('removeToken')
            ->once()
            ->with('is.valid.token')
            ->andReturnTrue();

        $this->jwt->shouldReceive('unsetToken')->once()->andReturnSelf();

        $this->guard->setToken('is.valid.token');
        $this->guard->logout();

        $this->assertNull($this->guard->getUser());
    }
}
