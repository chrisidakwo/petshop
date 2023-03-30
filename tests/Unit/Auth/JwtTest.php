<?php

namespace Tests\Unit\Auth;

use App\Auth\Contracts\JwtSubject;
use App\Auth\Jwt;
use App\Auth\Providers\JwtProvider;
use App\Exceptions\InvalidBearerToken;
use App\Exceptions\JwtException;
use App\Http\Parsers\AuthHeader;
use Illuminate\Http\Request;
use Mockery;
use Tests\Models\TestUser1;
use Tests\Models\TestUser2;
use Tests\TestCase;

class JwtTest extends TestCase
{
    protected Jwt $jwt;
    protected JwtProvider|Mockery\MockInterface $provider;
    protected AuthHeader|Mockery\MockInterface $authHeader;

    public function setUp(): void
    {
        parent::setUp();

        $this->provider = new JwtProvider(
            'HW44otIOCgtPhTTZCJhJrzy962lK82U07Q2z25QLlaQrr3PQD8GyqrrRXnxxrJNZ',
            [
                'private' => base_path('tests/Keys/private.pem'),
                'public' => base_path('tests/Keys/public.pem'),
            ]
        );

        $this->authHeader = Mockery::mock(AuthHeader::class);

        $this->jwt = new Jwt(
            resolve(Request::class),
            [
                $this->authHeader,
            ],
            $this->provider
        );
    }

    public function testItGeneratesATokenWhenPassedAUser(): void
    {
        $user = new TestUser1;
        $token = $this->jwt->generateTokenFromUser($user);

        $this->assertNotEmpty($token);
    }

    public function testItPassesIfSubjectHashMatches(): void
    {
        $token = $this->jwt->generateTokenFromUser(new TestUser1);

        $this->assertTrue(
            $this->jwt
                ->setToken($token)
                ->checkSubjectModel(TestUser1::class),
        );
    }

    public function testShouldFailIfSubjectHasDoesNotMatch()
    {
        $token = $this->getToken(new TestUser2());

        $this->assertFalse(
            $this->jwt
                ->setToken($token)
                ->checkSubjectModel(TestUser1::class),
        );
    }

    public function testItCorrectlyParsesRequestForToken(): void
    {
        $token = $this->getToken();

        $this->authHeader->shouldReceive('parse')
            ->once()
            ->andReturn($token);

        $result = $this->jwt->parseRequestForToken();

        $this->assertNotEmpty($result);
    }

    public function testItThrowsExceptionWhenNoTokenInRequest(): void
    {
        $this->expectException(InvalidBearerToken::class);
        $this->expectExceptionMessage('The token could not be parsed from the request');

        $this->authHeader->shouldReceive('parse')
            ->once()
            ->andReturn(false);

        $result = $this->jwt->parseRequestForToken();
    }

    public function testIsShouldValidateAndPassAWellFormedToken(): void
    {
        $result = $this->jwt->setToken('is.a.token');

        $this->assertNotEmpty($result);
        $this->assertInstanceOf(Jwt::class, $result);
    }

    public function testThatValidateTokenThrowsExceptionsWhenNotTokenIsPresent(): void
    {
        $this->expectException(JwtException::class);
        $this->expectExceptionMessage('Token is required');

        $this->jwt->validateToken();
    }

    public function testThatValidateTokenReturnsFalseWhenTokenIsMalformed(): void
    {
        $this->jwt->setToken('is.not.valid_token');

        $this->assertFalse($this->jwt->validateToken());
    }

    public function testThatValidateTokenReturnsDecodedTokenWhenTokenIsWellFormed(): void
    {
        $token = $this->getToken();

        $this->jwt->setToken($token);

        $result = $this->jwt->validateToken();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('sub', $result);
        $this->assertArrayHasKey('prv', $result);
    }



    protected function getToken(JwtSubject|null $subject = null): string
    {
        return $this->jwt->generateTokenFromUser($subject ?: new TestUser1());
    }
}
