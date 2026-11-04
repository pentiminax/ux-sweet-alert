<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\EventListener;

use Pentiminax\UX\SweetAlert\Delivery\AlertDelivery;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

#[AsEventListener]
class RenderAlertListener
{
    public function __construct(
        private readonly AlertDelivery $delivery,
    ) {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $this->delivery->deliver($event->getRequest(), $event->getResponse());
    }
}
