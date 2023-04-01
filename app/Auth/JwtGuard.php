<?php

declare(strict_types=1);

namespace App\Auth;

use App\Auth\Contracts\JwtSubject;
use App\Events\UserLoggedIn;
use App\Exceptions\InvalidBearerToken;
use App\Exceptions\JwtException;
use App\Http\Services\JwtTokenService;
use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JwtGuard implements Guard
{
    use GuardHelpers;

    protected Request $request;
    protected string $token;
    protected Jwt $jwt;
    protected JwtTokenService $jwtTokenService;
    protected Authenticatable|null $lastAttempted;

    public function __construct(
        UserProvider $provider,
        Request $request,
        Jwt $jwt,
        JwtTokenService $jwtTokenService,
    ) {
        $this->provider = $provider;
        $this->request = $request;
        $this->jwt = $jwt;
        $this->jwtTokenService = $jwtTokenService;
        $this->token = '';
    }

    /**
     * {@inheritdoc}
     *
     * @throws JwtException
     */
    public function user(): ?Authenticatable
    {
        if ($this->user !== null) {
            return $this->user;
        }

        $token = $this->getToken();

        if ($token && ($result = $this->jwt->validateToken()) && $this->validateSubject()) {
            /** @var array<string, mixed> $result */
            $tokenExists = $this->jwtTokenService->find($token);

            if ($tokenExists === null) {
                return null;
            }

            $this->user = $this->provider->retrieveById($result['sub']);

            return $this->user;
        }

        return null;
    }

    /**
     * @param array<string, string> $credentials
     *
     * @throws JwtException
     */
    public function validate(array $credentials = []): bool
    {
        return (bool) $this->attempt($credentials, false);
    }

    /**
     * @param array<string, string> $credentials
     *
     * @throws JwtException
     */
    public function attempt(array $credentials = [], bool $login = true): bool|string
    {
        $this->lastAttempted = $user = $this->provider->retrieveByCredentials($credentials);

        if ($user !== null && $this->hasValidCredentials($user, $credentials)) {
            return $login ? $this->login($user) : true;
        }

        return false;
    }

    /**
     * @throws JwtException
     */
    public function login(JwtSubject|Authenticatable $user): string
    {
        $token = $this->jwt->generateTokenFromUser($user); // @phpstan-ignore-line

        $this->setToken($token)->setUser($user); // @phpstan-ignore-line

        event(new UserLoggedIn($user));

        return $token;
    }

    public function authenticate(): Authenticatable|false
    {
        $token = $this->getToken();
        if ($token === null) {
            return false;
        }

        $jwtToken = $this->jwtTokenService->find($token);
        if ($jwtToken === null) {
            throw InvalidBearerToken::fromMessage('Failed to authenticate user');
        }

        $decodedToken = $this->jwt->getDecodedToken();
        if (! $user = $this->provider->retrieveById($decodedToken['sub'])) {
            return false;
        }

        $this->jwtTokenService->updateLastUsed($token);

        return $user;
    }

    public function logout(): void
    {
        $this->jwtTokenService->removeToken(
            is_string($this->token) && strlen($this->token) > 0 ? $this->token : (string) $this->jwt->getToken()
        );

        $this->user = null;

        $this->token = '';

        $this->jwt->unsetToken();
    }

    public function setToken(string $token): self
    {
        $this->jwt->setToken($token);

        $this->token = $token;

        return $this;
    }

    public function getUser(): ?Authenticatable
    {
        return $this->user;
    }

    public function getLastAttempted(): ?Authenticatable
    {
        return $this->lastAttempted;
    }

    public function setRequest(Request $request): static
    {
        $this->request = $request;

        return $this;
    }

    protected function getToken(): ?string
    {
        if (isset($this->token) && strlen($this->token) > 0) {
            return $this->token;
        }

        return $this->jwt->parseRequestForToken();
    }

    /**
     * @throws JwtException
     */
    protected function validateSubject(): bool
    {
        if (! method_exists($this->provider, 'getModel')) {
            return true;
        }

        $model = $this->provider->getModel();

        return $this->jwt->checkSubjectModel($model);
    }

    /**
     * @param array<string, string> $credentials
     */
    protected function hasValidCredentials(Authenticatable|null $user, array $credentials): bool
    {
        return $user !== null && $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * @throws JwtException
     */
    protected function requireToken(): Jwt
    {
        if (! $this->jwt->getDecodedToken()) {
            throw new JwtException('Token could not be parsed from the request.');
        }

        return $this->jwt;
    }
}
