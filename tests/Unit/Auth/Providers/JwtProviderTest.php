<?php

namespace Tests\Unit\Auth\Providers;

use App\Auth\Providers\JwtProvider;
use App\Exceptions\JwtException;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Lcobucci\JWT\Signer\Key\FileCouldNotBeRead;
use Tests\TestCase;

class JwtProviderTest extends TestCase
{
    protected Carbon $now;

    protected function setUp(): void
    {
        parent::setUp();

        $this->now = Carbon::now();
    }

    public function testCanEncodeAndDecodeJwtTokens(): void
    {
        $payload = $this->getPayload();

        $provider = $this->getProvider();

        $token = $provider->encode($payload);

        $header = json_decode(base64_decode(explode('.', $token)[0]), true);

        $this->assertEquals('JWT', $header['typ']);
        $this->assertEquals('RS512', $header['alg']);

        $decodedToken = $provider->decode($token);

        $this->assertEquals('1', $decodedToken['sub']);
        $this->assertEquals('/lorem-url', $decodedToken['iss']);
        $this->assertEquals('chris.idakwo@gmail.com', $decodedToken['custom']);
        $this->assertEquals($payload['exp'], $decodedToken['exp']->getTimestamp());
        $this->assertEquals($payload['iat'], $decodedToken['iat']->getTimestamp());
    }

    public function testItThrowsAnExceptionWhenEncodingAnInvalidPayload(): void
    {
        $this->expectException(JwtException::class);
        $this->expectExceptionMessage(
            'Provided payload must contain all the required claims set in the [auth.jwt.required_claims] config'
        );

        // missing nbf
        $payload = [
            'sub' => 1,
            'exp' => $this->now->getTimestamp() + 3600,
            'iat' => $this->now->getTimestamp(),
            'iss' => '/lorem/url',
            'email' => 'chris.idakwo@gmail.com',
        ];

        $this->getProvider()->encode($payload);
    }

    public function testItThrowsAnExceptionOnAnInvalidTokenValue(): void
    {
        $this->expectException(JwtException::class);
        $this->expectExceptionMessage('Could not decode the provided token.');

        $this->getProvider(Str::random(5))->decode('invalid.Toke.n');
    }

    /**
     * @throws JwtException
     */
    public function testItThrowsAnAExceptionOnAnInvalidPrivateKey(): void
    {
        $this->expectException(FileCouldNotBeRead::class);
        $this->expectExceptionMessage('The path "path/to/nothing.pem" does not contain a valid key file');

        $this->getProvider(
            Str::random(24),
            [
                'private' => 'path/to/nothing.pem',
            ]
        )->encode($this->getPayload());
    }

    /**
     * @throws JwtException
     */
    public function testItThrowsAnAExceptionOnAnInvalidPublicKey(): void
    {
        $this->expectException(FileCouldNotBeRead::class);
        $this->expectExceptionMessage('The path "path/to/nothing.pem" does not contain a valid key file');

        $this->getProvider(
            Str::random(24),
            [
                'public' => 'path/to/nothing.pem',
            ]
        )->encode($this->getPayload());
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function getPayload(array $payload = []): array
    {
        return array_merge([
            'sub' => '1',
            'exp' => $this->now->getTimestamp() + 3600,
            'iat' => $this->now->getTimestamp(),
            'nbf' => Carbon::now()->getTimestamp(),
            'jti' => Str::random(12),
            'iss' => '/lorem-url',
            'custom' => 'chris.idakwo@gmail.com',
        ], $payload);
    }

    /**
     * @throws JwtException
     */
    public function getProvider(
        string $secretKey = null,
        array $payload = [],
    ): JwtProvider {
        return new JwtProvider(
            $secretKey === null ? $this->getTestSecretKey() : $secretKey,
            array_merge([
                'private' => $this->getTestPrivateKey(),
                'public' => $this->getTestPublicKey(),
            ], $payload),
        );
    }

    public function getTestPrivateKey(): string
    {
        return base_path('tests/Keys/private.pem');
    }

    public function getTestPublicKey(): string
    {
        return base_path('tests/Keys/public.pem');
    }

    public function getTestSecretKey(): string
    {
        return 'tNLBusVcRts2Wq4YN94a30uG6g7VvOQwInrrsnvnTMTWYZx9MxdxiPG0ArDM7euY';
    }
}
