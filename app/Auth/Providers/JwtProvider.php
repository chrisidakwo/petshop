<?php

declare(strict_types=1);

namespace App\Auth\Providers;

use App\Auth\Contracts\Providers\JWT;
use App\Exceptions\JwtException;
use DateTimeImmutable;
use Exception;
use Illuminate\Support\Arr;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Ecdsa\Sha384;
use Lcobucci\JWT\Signer\Ecdsa\Sha512;
use Lcobucci\JWT\Signer\Rsa;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\UnencryptedToken;

class JwtProvider implements JWT
{
    public const ALGO_RS256 = 'RS256';
    public const ALGO_RS384 = 'RS384';
    public const ALGO_RS512 = 'RS512';
    public const ALGO_ES256 = 'ES256';
    public const ALGO_ES384 = 'ES384';
    public const ALGO_ES512 = 'ES512';

    protected Configuration $config;
    protected string $secret;
    protected string $algo;
    /**
     * @var array<string>
     */
    protected array $keys;

    /**
     * Signers that this provider supports.
     *
     * @var array<string, string>
     */
    protected array $signers = [
        self::ALGO_RS256 => Rsa\Sha256::class,
        self::ALGO_RS384 => Rsa\Sha384::class,
        self::ALGO_RS512 => Rsa\Sha512::class,
        self::ALGO_ES256 => Sha256::class,
        self::ALGO_ES384 => Sha384::class,
        self::ALGO_ES512 => Sha512::class,
    ];

    /**
     * @param array<string, string> $keys
     *
     * @throws JwtException
     */
    public function __construct(string $secret, string $algo, array $keys)
    {
        $this->secret = $secret;
        $this->algo = $algo;
        $this->keys = $keys;

        $this->validateKeys($keys);

        $this->config = Configuration::forAsymmetricSigner(
            $this->getSigner(),
            $this->getSigningKey(),
            $this->getVerificationKey(),
        );
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

        // TODO: Validate token. While testing, noticed that a token might be encoded with a different
        //   secret (passphrase) and this method would still return the token. We need to carry out some extra checks
        //   on the token

        return $parsedToken->claims()->all();
    }

    public function getSecretKey(): string
    {
        return $this->keys['secret'] ?? '';
    }

    /**
     * @throws JwtException
     */
    protected function getSigner(): Signer
    {
        if (! array_key_exists($this->algo, $this->signers)) {
            throw new JwtException('The provided algorithm is not supported');
        }

        /** @var Signer $signer */
        return new $this->signers[$this->algo]();
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

        // 'iss', 'iat', 'exp', 'nbf', 'sub', 'jti'

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
        unset($payload[RegisteredClaims::ID]);
        unset($payload[RegisteredClaims::SUBJECT]);
        unset($payload[RegisteredClaims::ISSUER]);
        unset($payload[RegisteredClaims::ISSUED_AT]);
        unset($payload[RegisteredClaims::NOT_BEFORE]);
        unset($payload[RegisteredClaims::EXPIRATION_TIME]);

        foreach ($payload as $key => $value) {
            // @phpstan-ignore-next-line
            $builder = $builder->withClaim($key, $value);
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
