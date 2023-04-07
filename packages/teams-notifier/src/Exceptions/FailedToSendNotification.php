<?php

declare(strict_types=1);

namespace Petshop\TeamsNotifier\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpException;

class FailedToSendNotification extends HttpException
{

}
