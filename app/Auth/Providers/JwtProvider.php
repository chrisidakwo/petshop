<?php

declare(strict_types=1);

namespace App\Auth\Providers;

use App\Auth\Contracts\Providers\JWT;
use App\Exceptions\InvalidBearerToken;
use App\Exceptions\JwtException;
use DateTimeImmutable;
use Exception;
use Illuminate\Support\Arr;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\SignedWith;

class JwtProvider implements JWT
{
    protected Configuration $config;
    protected string $secret;
    /**
     * @var array<string>
     */
    protected array $keys;

    /**
     * @param array<string, string> $keys
     *
     * @throws JwtException
     */
    public function __construct(string $secret, array $keys)
    {
        $this->secret = $secret;
        $this->keys = $keys;

        $this->validateKeys($keys);

        $this->config = $this->buildConfig();
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @throws JwtException
     */
    public function encode(array $payload): string
    {
        $tokenBuilder = $this->getTokenBuilder($payload);

        try {
            return $tokenBuilder
                ->getToken($this->config->signer(), $this->config->signingKey())
                ->toString();
        } catch (Exception $exception) {
            throw new JwtException(
                $exception->getMessage(),
                $exception->getCode(),
                $exception,
            );
        }
    }

    /**
     * @return array<string, mixed>
     *
     * @throws JwtException
     */
    public function decode(string $token): array
    {
        if (strlen($token) === 0) {
            throw new JwtException('Cannot decode an empty token string');
        }

        try {
            $parsedToken = $this->config->parser()->parse($token);
        } catch (Exception $exception) {
            throw new JwtException(
                'Could not decode the provided token. [' . $exception->getMessage() . ']',
                $exception->getCode(),
                $exception,
            );
        }

        if (! $parsedToken instanceof UnencryptedToken) {
            throw new JwtException('Could not properly decode the provided token', 500);
        }

        if (! $this->config->validator()->validate($parsedToken, ...$this->config->validationConstraints())) {
            throw InvalidBearerToken::fromMessage('Token could not be validated', 500);
        }

        return $parsedToken->claims()->all();
    }

    public function getSecretKey(): string
    {
        return $this->secret;
    }

    protected function buildConfig(): Configuration
    {
        $signer = new Signer\Rsa\Sha512();

        $config = Configuration::forAsymmetricSigner(
            $signer,
            $this->getSigningKey(),
            $this->getVerificationKey(),
        );

        $config->setValidationConstraints(
            new SignedWith($signer, $this->getVerificationKey()),
        );

        return $config;
    }

    protected function getSigningKey(): Signer\Key\InMemory
    {
        /** @var non-empty-string $privateKey */
        $privateKey = $this->keys['private'];

        return Signer\Key\InMemory::file($privateKey, $this->getSecretKey());
    }

    protected function getVerificationKey(): Signer\Key\InMemory
    {
        /** @var non-empty-string $publicKey */
        $publicKey = $this->keys['public'];

        return Signer\Key\InMemory::file($publicKey);
    }

    /**
     * @param array<string, mixed> $keys
     *
     * @throws JwtException
     */
    protected function validateKeys(array $keys): void
    {
        // Validate private key
        if (! Arr::get($keys, 'private')) {
            throw new JwtException('Please set the private key');
        }

        // Validate public key
        if (! Arr::get($keys, 'public')) {
            throw new JwtException('Please set the public key');
        }
    }

    /**
     * @param array<string, string> $payload
     *
     * @throws JwtException
     */
    protected function getTokenBuilder(array $payload): Builder
    {
        $this->validateClaims($payload);

        /** @var non-empty-string $jti */
        $jti = $payload[RegisteredClaims::ID];

        /** @var non-empty-string $sub */
        $sub = $payload[RegisteredClaims::SUBJECT];

        /** @var non-empty-string $iss */
        $iss = $payload[RegisteredClaims::ISSUER];

        /** @var DateTimeImmutable $iat */
        $iat = DateTimeImmutable::createFromFormat('U', (string) $payload[RegisteredClaims::ISSUED_AT]);

        /** @var DateTimeImmutable $nbf */
        $nbf = DateTimeImmutable::createFromFormat('U', (string) $payload[RegisteredClaims::NOT_BEFORE]);

        /** @var DateTimeImmutable $exp */
        $exp = DateTimeImmutable::createFromFormat('U', (string) $payload[RegisteredClaims::EXPIRATION_TIME]);

        $builder = $this->config->builder()
            ->identifiedBy($jti)
            ->relatedTo($sub)
            ->issuedBy($iss)
            ->issuedAt($iat)
            ->canOnlyBeUsedAfter($nbf)
            ->expiresAt($exp);

        // Unset pre-expected claims, so we can add other custom claims (eg: as might be defined on a model)
        $payload = Arr::except($payload, [
            RegisteredClaims::ID, RegisteredClaims::SUBJECT, RegisteredClaims::ISSUER,
            RegisteredClaims::ISSUED_AT, RegisteredClaims::NOT_BEFORE, RegisteredClaims::EXPIRATION_TIME,
        ]);

        foreach ($payload as $key => $value) {
            $builder = $builder->withClaim($key, $value); // @phpstan-ignore-line
        }

        return $builder;
    }

    /**
     * Ensure that all necessary claims are present
     *
     * @param array<string, string> $payload
     *
     * @throws JwtException
     */
    protected function validateClaims(array $payload): void
    {
        $claims = config('auth.jwt.required_claims');

        if (count(array_intersect($claims, array_keys($payload))) !== count($claims)) {
            throw new JwtException(
                'Provided payload must contain all the required claims set in the [auth.jwt.required_claims] config'
            );
        }
    }
}
