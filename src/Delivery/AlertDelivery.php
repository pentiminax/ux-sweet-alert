<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Delivery;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Drains the Alert queue into the one transport the current response can display.
 *
 * @internal
 */
final class AlertDelivery
{
    public const HX_EVENT_KEY = 'ux-sweet-alert:alert:added';

    public const JSON_KEY = 'ux_sweet_alert';

    public function __construct(
        private readonly AlertManagerInterface $alertManager,
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @return \Pentiminax\UX\SweetAlert\Model\Alert[]
     */
    public function forView(): array
    {
        $request = $this->requestStack->getCurrentRequest();

        return null !== $request && $this->canUseSession($request) ? $this->alertManager->getAlerts() : [];
    }

    public function deliver(Request $request, Response $response): void
    {
        if ($response->isRedirection() || !$this->canUseSession($request)) {
            return;
        }

        $contentType = $response->headers->get('Content-Type') ?? '';

        if ($request->headers->has('HX-Request')) {
            $this->deliverHxTrigger($response);
        } elseif (false !== $response->getContent() && str_contains($contentType, 'text/vnd.turbo-stream.html')) {
            $alerts = $this->alertManager->getAlerts();
            if ([] !== $alerts) {
                $response->setContent($response->getContent().$this->renderTurboStreams($alerts));
            }
        } elseif (
            false !== $response->getContent() // streamed/binary bodies cannot be modified
            && str_contains($contentType, 'text/html')
            && class_exists(\Symfony\UX\Turbo\TurboBundle::class)
            && $request->headers->has('Turbo-Frame')
        ) {
            $this->deliverInTurboFrame($request, $response);
        } elseif ($response instanceof JsonResponse) {
            try {
                $data = json_decode((string) $response->getContent(), false, 512, \JSON_THROW_ON_ERROR);
            } catch (\JsonException) {
                return;
            }
            if (!$data instanceof \stdClass || property_exists($data, self::JSON_KEY)) {
                return;
            }

            $alerts = $this->alertManager->getAlerts();
            if ([] !== $alerts) {
                $data->{self::JSON_KEY} = array_map(static fn ($alert) => $alert->jsonSerialize(), $alerts);
                // An alert with invalid UTF-8 must not throw once the queue is drained.
                $response->setEncodingOptions($response->getEncodingOptions() | \JSON_INVALID_UTF8_SUBSTITUTE);
                $response->setData($data);
            }
        }
    }

    // Never start a session just to look for alerts.
    private function canUseSession(Request $request): bool
    {
        return !$request->attributes->getBoolean('_stateless')
            && $request->hasSession()
            && ($request->getSession()->isStarted() || $request->hasPreviousSession());
    }

    private function deliverHxTrigger(Response $response): void
    {
        $existing = $response->headers->get('HX-Trigger');
        $trigger  = new \stdClass();

        if (null !== $existing && '' !== $existing) {
            $decoded = json_decode($existing);
            if (\JSON_ERROR_NONE === json_last_error()) {
                if (!$decoded instanceof \stdClass) {
                    return;
                }
                $trigger = $decoded;
            } else {
                $trigger = (object) array_fill_keys(array_filter(array_map('trim', explode(',', $existing))), null);
            }
        }

        $alerts = $this->alertManager->getAlerts();
        if ([] === $alerts) {
            return;
        }

        $trigger->{self::HX_EVENT_KEY} = ['alerts' => array_map(static fn ($alert) => $alert->jsonSerialize(), $alerts)];

        $response->headers->set('HX-Trigger', json_encode($trigger, \JSON_THROW_ON_ERROR | \JSON_INVALID_UTF8_SUBSTITUTE));
    }

    // Turbo keeps only the requested frame of a frame response, so streams must go inside it.
    private function deliverInTurboFrame(Request $request, Response $response): void
    {
        $content = (string) $response->getContent();
        $id      = (string) $request->headers->get('Turbo-Frame');
        $pattern = '/<turbo-frame\b[^>]*\bid=(["\'])'.preg_quote($id, '/').'\1[^>]*>/i';

        if (1 !== preg_match($pattern, $content, $m, \PREG_OFFSET_CAPTURE)) {
            return;
        }

        $alerts = $this->alertManager->getAlerts();
        if ([] === $alerts) {
            return;
        }

        $offset = $m[0][1] + \strlen($m[0][0]);
        $response->setContent(substr($content, 0, $offset).$this->renderTurboStreams($alerts).substr($content, $offset));
    }

    private function renderTurboStreams(array $alerts): string
    {
        $result = '';
        foreach ($alerts as $alert) {
            $result .= $this->twig->render('@SweetAlert/turbo/alert.html.twig', ['alert' => $alert]);
        }

        return $result;
    }
}
