<?php

namespace Pentiminax\UX\SweetAlert\Twig\Components;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\Result;
use Symfony\Contracts\Service\Attribute\Required;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;
use Symfony\UX\TwigComponent\Attribute\ExposeInTemplate;

class ConfirmButton
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public string $title = '';

    #[LiveProp]
    public string $text = '';

    #[LiveProp]
    public bool $showCancelButton = true;

    #[LiveProp]
    public string $icon = Icon::QUESTION->value;

    #[LiveProp]
    public string $callback = '';

    #[LiveProp]
    public string $customClass = '';

    #[LiveProp]
    public string $confirmButtonText = 'OK';

    #[LiveProp]
    public string $cancelButtonText = 'Cancel';

    #[ExposeInTemplate(getter: 'isDisabled')]
    public bool $disabled = false;

    protected ?Result $result = null;

    protected ?TranslatorInterface $translator = null;

    private readonly SweetAlertContextInterface $context;

    #[LiveListener('alertAdded')]
    public function alertAdded(): void
    {
        $alert = Alert::new(
            title: $this->translate($this->title),
            text: $this->translate($this->text),
            icon: Icon::from($this->icon),
            position: Position::CENTER,
            customClass: $this->customClass()
        );

        $alert
            ->confirmButtonText($this->translate($this->confirmButtonText))
            ->cancelButtonText($this->translate($this->cancelButtonText));

        if ($this->showCancelButton) {
            $alert->withCancelButton();
        }

        $this->context->addAlert($alert);

        $this->dispatchBrowserEvent('ux-sweet-alert:alert:added', [
            'alert' => $alert,
            'callback' => $this->callback
        ]);
    }

    #[LiveAction]
    public function callbackAction(#[LiveArg] array $result, #[LiveArg] array $args = []): mixed
    {
        $this->result = Result::fromArray($result);

        $this->dispatchBrowserEvent('ux-sweet-alert:callback', [
            'result' => $result,
            'args' => $args
        ]);

        return null;
    }

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

    private function customClass(): array
    {
        return empty($this->customClass) ? [] : json_decode($this->customClass, true);
    }

    private function translate(string $message): string
    {
        return $this->translator?->trans($message) ?? $message;
    }
}
