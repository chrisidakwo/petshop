<?php

declare(strict_types=1);

namespace Petshop\TeamsNotifier;

use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notification;
use Petshop\TeamsNotifier\Exceptions\FailedToSendNotification;
use RuntimeException;

class TeamsChannel
{
    protected TeamsNotifier $teamsNotifier;

    public function __construct(TeamsNotifier $teamsNotifier)
    {
        $this->teamsNotifier = $teamsNotifier;
    }

    /**
     * Send the provided notification.
     *
     * @throws FailedToSendNotification|GuzzleException
     */
    public function sendNotification(mixed $notifiable, Notification $notification)
    {
        if (! $route = $notifiable->routeNotificationFor('microsoftTeams')) {
            return;
        }

        if (! $message = $notification->toMicrosoftTeams($notifiable)) {
            return;
        }

        if (! $message instanceof TeamsMessage) {
            $messageClass = TeamsMessage::class;

            throw new RuntimeException("Message must be an instance of {$messageClass}");
        }

        return $this->teamsNotifier->send($route, $message);
    }

}
