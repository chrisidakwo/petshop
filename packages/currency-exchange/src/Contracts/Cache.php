<?php

declare(strict_types=1);

namespace PetShop\CurrencyExchange\Contracts;

interface Cache
{
    /**
     * Get an item out of cache.
     *
     * If found, it returns the item in the string format as saved, else return null.
     */
    public function getItem(string $key): null|string;

    /**
     * Save a new item to cache
     */
    public function saveItem(string $key, mixed $value): void;
}
