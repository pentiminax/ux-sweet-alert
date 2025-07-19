<?php

namespace Pentiminax\UX\SweetAlert\EventListener;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\UX\Turbo\TurboBundle;
use Twig\Environment;

#[AsEventListener]
class RenderAlertListener
{
    public function __construct(
        private readonly AlertManagerInterface $alertManager,
        private readonly Environment           $twig
    ) {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if (!$this->shouldInjectAlerts($response)) {
            return;
        }

        $alerts = $this->alertManager->getAlerts();

        if (empty($alerts)) {
            return;
        }

        $responseContent = '';
        foreach ($alerts as $alert) {
            $responseContent .= $this->twig->render('@SweetAlert/turbo/alert.html.twig', [
                'alert' => $alert
            ]);
        }

        $response->headers->set('Content-Type', TurboBundle::STREAM_MEDIA_TYPE);

        $response
            ->setContent($responseContent);
    }

    private function shouldInjectAlerts(Response $response): bool
    {
        if ($response->isRedirection()) {
            return false;
        }

        return true;
    }
}