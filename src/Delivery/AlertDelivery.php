<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Delivery;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\UX\Turbo\TurboBundle;
use Twig\Environment;

/**
 * Drains the Alert queue into the one transport the current response can display.
 *
 * Each transport checks that it applies before draining, so alerts stay queued
 * for the next response when none does.
 *
 * @internal
 */
final class AlertDelivery
{
    public const HX_EVENT_KEY = 'ux-sweet-alert:alert:added';

    public const JSON_KEY = 'ux_sweet_alert';

    public const PRE_CONFIRM_HEADER = 'X-SweetAlert-PreConfirm';

    private const HX_REQUEST_HEADER = 'HX-Request';

    private const HX_TRIGGER_HEADER = 'HX-Trigger';

    private const TURBO_FRAME_HEADER = 'Turbo-Frame';

    private const TURBO_STREAM_TEMPLATE = '@SweetAlert/turbo/alert.html.twig';

    // Group 1: the opening tag's attribute list; quoted values may contain `>`.
    private const FRAME_OPENING_TAG_PATTERN = '/<turbo-frame(?=[\s\/>])((?:\s+[^\s"\'>\/=]+(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s"\'=<>`]+))?)*)\s*\/?>/i';

    // Group 1: name; groups 2-4: double-quoted, single-quoted or unquoted value.
    private const HTML_ATTRIBUTE_PATTERN = '/([^\s"\'>\/=]+)(?:\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s"\'=<>`]+)))?/';

    public function __construct(
        private readonly AlertManagerInterface $alertManager,
        private readonly Environment $twig,
        private readonly RequestStack $requestStack,
    ) {
    }

    /**
     * @return Alert[]
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

        match (true) {
            $request->headers->has(self::HX_REQUEST_HEADER) => $this->deliverHxTrigger($response),
            $this->isTurboStreamResponse($response)         => $this->appendTurboStreams($response),
            $this->isTurboFrameRequest($request, $response) => $this->deliverInTurboFrame($request, $response),
            $response instanceof JsonResponse               => $this->deliverJson($response),
            default                                         => null,
        };
    }

    // Never start a session just to look for alerts. Pre-confirm requests must not drain the queue: the open modal would swallow the alerts.
    private function canUseSession(Request $request): bool
    {
        return !$request->headers->has(self::PRE_CONFIRM_HEADER)
            && !$request->attributes->getBoolean('_stateless')
            && $request->hasSession()
            && ($request->getSession()->isStarted() || $request->hasPreviousSession());
    }

    private function isTurboStreamResponse(Response $response): bool
    {
        return $this->hasModifiableBody($response)
            && str_contains($response->headers->get('Content-Type') ?? '', 'text/vnd.turbo-stream.html');
    }

    private function isTurboFrameRequest(Request $request, Response $response): bool
    {
        return $this->hasModifiableBody($response)
            && str_contains($response->headers->get('Content-Type') ?? '', 'text/html')
            && class_exists(TurboBundle::class)
            && $request->headers->has(self::TURBO_FRAME_HEADER);
    }

    // Streamed and binary responses return false and cannot be appended to.
    private function hasModifiableBody(Response $response): bool
    {
        return false !== $response->getContent();
    }

    private function deliverHxTrigger(Response $response): void
    {
        $trigger = $this->existingTrigger($response->headers->get(self::HX_TRIGGER_HEADER));
        if (null === $trigger) {
            return;
        }

        $alerts = $this->alertManager->getAlerts();
        if ([] === $alerts) {
            return;
        }

        $trigger->{self::HX_EVENT_KEY} = ['alerts' => $this->serialize($alerts)];

        $response->headers->set(self::HX_TRIGGER_HEADER, json_encode($trigger, \JSON_THROW_ON_ERROR | \JSON_INVALID_UTF8_SUBSTITUTE));
    }

    /**
     * Returns the existing HX-Trigger as an object to merge into, or null when it
     * holds JSON that is not an object (we must not rewrite it).
     */
    private function existingTrigger(?string $header): ?\stdClass
    {
        if (null === $header || '' === $header) {
            return new \stdClass();
        }

        try {
            $decoded = json_decode($header, false, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            // Plain form: comma-separated event names.
            return (object) array_fill_keys(array_filter(array_map('trim', explode(',', $header))), null);
        }

        return $decoded instanceof \stdClass ? $decoded : null;
    }

    private function appendTurboStreams(Response $response): void
    {
        $alerts = $this->alertManager->getAlerts();
        if ([] === $alerts) {
            return;
        }

        $response->setContent($response->getContent().$this->renderTurboStreams($alerts));
    }

    // Turbo keeps only the requested frame of a frame response, so streams must go inside it.
    private function deliverInTurboFrame(Request $request, Response $response): void
    {
        $content = (string) $response->getContent();
        $offset  = $this->findFrameContentOffset($content, (string) $request->headers->get(self::TURBO_FRAME_HEADER));

        if (null === $offset) {
            return;
        }

        $alerts = $this->alertManager->getAlerts();
        if ([] === $alerts) {
            return;
        }

        $response->setContent(substr($content, 0, $offset).$this->renderTurboStreams($alerts).substr($content, $offset));
    }

    /**
     * Returns the offset right after the opening tag of the frame whose `id` attribute
     * is exactly $id, or null. Attributes are tokenized per HTML so `data-id` or an
     * `id=` inside another attribute's value never match; names are case-insensitive,
     * values are not, and the first `id` attribute of a tag wins.
     */
    private function findFrameContentOffset(string $content, string $id): ?int
    {
        preg_match_all(self::FRAME_OPENING_TAG_PATTERN, $content, $tags, \PREG_SET_ORDER | \PREG_OFFSET_CAPTURE);

        foreach ($tags as $tag) {
            preg_match_all(self::HTML_ATTRIBUTE_PATTERN, $tag[1][0], $attributes, \PREG_SET_ORDER | \PREG_UNMATCHED_AS_NULL);

            foreach ($attributes as $attribute) {
                if ('id' === strtolower($attribute[1])) {
                    if ($id === ($attribute[2] ?? $attribute[3] ?? $attribute[4] ?? '')) {
                        return $tag[0][1] + \strlen($tag[0][0]);
                    }

                    continue 2;
                }
            }
        }

        return null;
    }

    private function deliverJson(JsonResponse $response): void
    {
        try {
            $data = json_decode((string) $response->getContent(), false, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return;
        }

        if (!$data instanceof \stdClass || property_exists($data, self::JSON_KEY)) {
            return;
        }

        $alerts = $this->alertManager->getAlerts();
        if ([] === $alerts) {
            return;
        }

        $data->{self::JSON_KEY} = $this->serialize($alerts);
        // An alert with invalid UTF-8 must not throw once the queue is drained.
        $response->setEncodingOptions($response->getEncodingOptions() | \JSON_INVALID_UTF8_SUBSTITUTE);
        $response->setData($data);
    }

    /**
     * @param Alert[] $alerts
     */
    private function renderTurboStreams(array $alerts): string
    {
        return implode('', array_map(
            fn (Alert $alert): string => $this->twig->render(self::TURBO_STREAM_TEMPLATE, ['alert' => $alert]),
            $alerts,
        ));
    }

    /**
     * @param Alert[] $alerts
     *
     * @return array<int, array<string, mixed>>
     */
    private function serialize(array $alerts): array
    {
        return array_map(static fn (Alert $alert): array => $alert->jsonSerialize(), $alerts);
    }
}
