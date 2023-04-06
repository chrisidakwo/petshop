<?php

declare(strict_types=1);

namespace Petshop\CurrencyExchange\Providers\Ecb;

use Petshop\CurrencyExchange\Exception\ExchangeRateNotFound;
use SimpleXMLElement;

class EcbApiClient
{
    public function getData(string $url): SimpleXMLElement
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $data = curl_exec($ch);

        if (is_bool($data) && $data === false) {
            try {
                $data = file_get_contents($url);
            } catch (Exception $exception) {
                $provider = $this::class;
                throw new ExchangeRateNotFound(
                    "Could not retrieve rates from API [{$provider}]",
                    500,
                    $exception,
                );
            }
        }

        return (new SimpleXMLElement($data))->Cube[0]->Cube[0];
    }
}
