<?php

namespace Pentiminax\UX\SweetAlert\EventListener;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\ToastManagerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Twig\Environment;

#[AsEventListener]
class RenderAlertListener
{
    public function __construct(
        private readonly AlertManagerInterface $alertManager,
        private readonly ToastManagerInterface $toastManager,
        private readonly Environment           $twig
    ) {
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $response = $event->getResponse();

        if ($response->isRedirection()) {
            return;
        }

        $alerts = array_merge(
            $this->alertManager->getAlerts(),
            $this->toastManager->getToasts()
        );

        if (empty($alerts)) {
            return;
        }

        $turboStreams = $this->renderAlerts($alerts);

        if ($response instanceof JsonResponse) {
            $data = json_decode($response->getContent(), true) ?? [];
            $data['alerts'] = $turboStreams;
            $response->setData($data);

            return;
        }

        if ($this->isHtmlResponse($response)) {
            $content = $response->getContent() . $turboStreams;
            $response->setContent($content);
        }
    }

    private function renderAlerts(array $alerts): string
    {
        $result = '';
        foreach ($alerts as $alert) {
            $result .= $this->twig->render('@SweetAlert/turbo/alert.html.twig', [
                'alert' => $alert
            ]);
        }

        return $result;
    }

    private function isHtmlResponse(Response $response): bool
    {
        return str_contains($response->headers->get('Content-Type') ?? '', 'text/html');
    }
}