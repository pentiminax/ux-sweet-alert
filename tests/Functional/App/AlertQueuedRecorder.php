<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Functional\App;

use Pentiminax\UX\SweetAlert\Event\AlertQueuedEvent;
use Pentiminax\UX\SweetAlert\Event\BeforeAlertQueuedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

/**
 * Stamps every alert before it is queued and records the ones that were queued.
 */
final class AlertQueuedRecorder
{
    public const FOOTER = 'Stamped by a BeforeAlertQueuedEvent listener';

    /** @var string[] */
    public array $queuedTitles = [];

    #[AsEventListener]
    public function onBeforeAlertQueued(BeforeAlertQueuedEvent $event): void
    {
        $event->getAlert()->setFooter(self::FOOTER);
    }

    #[AsEventListener]
    public function onAlertQueued(AlertQueuedEvent $event): void
    {
        $this->queuedTitles[] = $event->getAlert()->getTitle();
    }
}
