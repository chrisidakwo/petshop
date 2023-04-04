<?php

declare(strict_types=1);

namespace App\Auth\Passwords;

use InvalidArgumentException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Auth\PasswordBrokerFactory;
use Illuminate\Auth\Passwords\DatabaseTokenRepository;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;

class PasswordBrokerManager implements PasswordBrokerFactory
{
    /**
     * The application instance.
     */
    protected Application $app;

    /**
     * The array of created "drivers".
     *
     * @var array<string, mixed>
     */
    protected array $brokers = [];

    /**
     * Create a new PasswordBroker manager instance.
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param array<string, mixed> $parameters
     */
    public function __call(string $method, array $parameters): mixed
    {
        return $this->broker()->{$method}(...$parameters);
    }

    /**
     * Attempt to get the broker from the local cache.
     *
     * @param string|null $name
     */
    public function broker($name = null): \Illuminate\Contracts\Auth\PasswordBroker|PasswordBroker
    {
        $name = $name ? $name : $this->getDefaultDriver();

        return $this->brokers[$name] ?? ($this->brokers[$name] = $this->resolve($name));
    }

    /**
     * Get the default password broker name.
     */
    public function getDefaultDriver(): string
    {
        return $this->app['config']['auth.defaults.passwords']; // @phpstan-ignore-line
    }

    /**
     * Set the default password broker name.
     */
    public function setDefaultDriver(string $name): void
    {
        $this->app['config']['auth.defaults.passwords'] = $name;
    }

    /**
     * Resolve the given broker.
     */
    protected function resolve(string $name): \Illuminate\Contracts\Auth\PasswordBroker|PasswordBroker
    {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        // The password broker uses a token repository to validate tokens and send user
        // password e-mails, as well as validating that password reset process as an
        // aggregate service of sorts providing a convenient interface for resets.
        return new PasswordBroker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'] ?? null) // @phpstan-ignore-line
        );
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param  array<string, mixed> $config
     */
    protected function createTokenRepository(array $config): TokenRepositoryInterface|DatabaseTokenRepository
    {
        $key = $this->app['config']['app.key']; // @phpstan-ignore-line

        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = $config['connection'] ?? null;

        return new DatabaseTokenRepository(
            $this->app['db']->connection($connection), // @phpstan-ignore-line
            $this->app['hash'], // @phpstan-ignore-line
            $config['table'],
            $key,
            $config['expire'],
            $config['throttle'] ?? 0
        );
    }

    /**
     * Get the password broker configuration.
     *
     * @return array<string, mixed>|null
     */
    protected function getConfig(string $name): ?array
    {
        return $this->app['config']["auth.passwords.{$name}"]; // @phpstan-ignore-line
    }
}
