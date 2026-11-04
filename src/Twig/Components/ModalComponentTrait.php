<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Twig\Components;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Delivery\AlertDelivery;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * State and helpers shared by ConfirmButton and InputModal.
 *
 * @internal
 */
trait ModalComponentTrait
{
    private const CALLBACK_EVENT = 'ux-sweet-alert:callback';

    protected ?TranslatorInterface $translator = null;

    private SweetAlertContextInterface $context;

    #[Required]
    public function setContext(SweetAlertContextInterface $context): void
    {
        $this->context = $context;
    }

    #[Required]
    public function setTranslator(?TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @return array<string, string>
     */
    private function customClass(): array
    {
        return $this->decodeJsonArray($this->customClass);
    }

    /**
     * Malformed or non-array JSON is ignored so a bad prop never breaks the modal.
     *
     * @return array<array-key, mixed>
     */
    private function decodeJsonArray(string $json): array
    {
        if ('' === $json) {
            return [];
        }

        try {
            $decoded = json_decode($json, true, flags: \JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            return [];
        }

        return \is_array($decoded) ? $decoded : [];
    }

    private function translate(string $message): string
    {
        return $this->translator?->trans($message) ?? $message;
    }

    private function queueAlert(Alert $alert): void
    {
        $this->context->addAlert($alert);

        $this->dispatchBrowserEvent(AlertDelivery::HX_EVENT_KEY, [
            'alert'    => $alert,
            'callback' => $this->callback,
        ]);
    }

    /**
     * @param array<string, mixed> $result
     * @param array<string, mixed> $args
     */
    private function dispatchCallback(array $result, array $args): void
    {
        $this->dispatchBrowserEvent(self::CALLBACK_EVENT, [
            'result' => $result,
            'args'   => $args,
        ]);
    }
}
