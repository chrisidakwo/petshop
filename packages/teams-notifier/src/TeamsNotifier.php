<?php

namespace Petshop\TeamsNotifier;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Petshop\TeamsNotifier\Exceptions\FailedToSendNotification;

class TeamsNotifier
{
    protected const BASE_API_URL = 'https://teamsapp.com/api';
    protected Client $apiClient;

    public function __construct(Client $client)
    {
        $this->apiClient = $client;
    }

    /**
     * Send a message to a Teams channel.
     *
     * @throws GuzzleException|FailedToSendNotification
     */
    public function send(mixed $webhook, TeamsMessage $message)
    {
        try {
            $response = $this->apiClient->request('POST', $webhook, [
                'body' => json_encode($message),
            ]);
        } catch (RequestException $exception) {

            throw new FailedToSendNotification(
                $exception->getCode(),
                $exception->getMessage(),
                $exception,
            );
        } catch (Exception $exception) {
            throw new FailedToSendNotification(
                'Could not properly communicate with Microsoft Teams',
                $exception->getCode(),
                $exception,
            );
        }

        return json_decode($response->getBody(), true);
    }

}
