<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\Contracts\JwtSubject;
use App\Auth\Validators\TokenValidator;
use App\Exceptions\InvalidBearerToken;
use App\Exceptions\JwtException;
use App\Http\Parsers\HttpParser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Jwt
{
    protected Request $request;
    protected string|null $token;
    /**
     * @var array<HttpParser>
     */
    protected array $parsers;
    protected Contracts\Providers\JWT $jwtProvider;

    /**
     * @param array<HttpParser> $parsers
     */
    public function __construct(Request $request, array $parsers, Contracts\Providers\JWT $jwtProvider)
    {
        $this->request = $request;
        $this->parsers = $parsers;
        $this->jwtProvider = $jwtProvider;

        $this->token = null;
    }

    public function setToken(?string $token): static
    {
        if ($token !== null) {
            $isValid = (new TokenValidator())->isValid($token);

            if (! $isValid) {
                throw InvalidBearerToken::fromMessage('The token could not be parsed from the request');
            }
        }

        $this->token = $token;

        return $this;
    }

    /**
     * @throws JwtException
     */
    public function generateTokenFromUser(JwtSubject $subject): string
    {
        $payload = $this->buildClaimsPayload($subject);

        return $this->jwtProvider->encode($payload);
    }

    /**
     * @return array<string, mixed>
     */
    public function buildClaimsPayload(JwtSubject $subject): array
    {
        $now = Carbon::now();

        return [
            'sub' => $subject->getSubjectIdentifier(),
            'prv' => $this->hashSubjectModel($subject::class),
            'iss' => $this->request->getHost(),
            'exp' => now()->addMinutes(config('auth.jwt.ttl'))->getTimestamp(),
            'iat' => $now->getTimestamp(),
            'nbf' => $now->getTimestamp(),
            'jti' => Str::random(),
        ];
    }

    public function parseRequestForToken(): ?string
    {
        $response = null;

        foreach ($this->parsers as $parser) {
            $response = $parser->parse($this->request);

            if ($response) {
                break;
            }
        }

        return $this->setToken($response)->token;
    }

    public function unsetToken(): self
    {
        $this->token = null;

        return $this;
    }

    /**
     * @return array<string, mixed>|bool
     *
     * @throws JwtException
     */
    public function validateToken(): bool|array
    {
        $this->assertTokenIsPresent();

        try {
            $isValid = (new TokenValidator())->isValid((string) $this->token);
            if (! $isValid) {
                return false;
            }

            return $this->getDecodedToken();
        } catch (JwtException) {
            return false;
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws JwtException
     */
    public function getDecodedToken(): array
    {
        return $this->jwtProvider->decode((string) $this->token);
    }

    /**
     * @throws JwtException
     */
    public function checkSubjectModel(string $model): bool
    {
        $prv = $this->getDecodedToken()['prv'];

        return $prv === null || $this->hashSubjectModel($model) === $prv;
    }

    /**
     * @throws JwtException
     */
    protected function assertTokenIsPresent(): void
    {
        if (! isset($this->token) || ! $this->token) {
            throw new JwtException('Token is required');
        }
    }

    protected function hashSubjectModel(string $model): string
    {
        return sha1($model);
    }
}
