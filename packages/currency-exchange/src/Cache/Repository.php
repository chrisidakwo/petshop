<?php

declare(strict_types=1);

namespace PetShop\CurrencyExchange\Cache;

use Illuminate\Contracts\Cache\Repository as LaravelRepository;
use PetShop\CurrencyExchange\Contracts\Cache;
use Psr\SimpleCache\InvalidArgumentException;

class Repository implements Cache
{
    protected LaravelRepository $laravelRepository;

    public function __construct(LaravelRepository $laravelRepository)
    {
        $this->laravelRepository = $laravelRepository;
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException
     */
    public function getItem(string $key): null|string
    {
        return $this->laravelRepository->get($key);
    }

    /**
     * @inheritDoc
     *
     * @throws InvalidArgumentException
     */
    public function saveItem(string $key, mixed $value): bool
    {
        /** @var int $dateTime */
        $dateTime = strtotime("+7 days");

        return $this->laravelRepository->set($key, $value, $dateTime);
    }
}
