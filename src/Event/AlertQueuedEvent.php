<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Event;

use Pentiminax\UX\SweetAlert\Model\Alert;
use Symfony\Contracts\EventDispatcher\Event;

final class AlertQueuedEvent extends Event
{
    public function __construct(
        private readonly Alert $alert,
    ) {
    }

    public function getAlert(): Alert
    {
        return $this->alert;
    }
}
