<?php

namespace Pentiminax\UX\SweetAlert\Twig\Components;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\InputType;
use Pentiminax\UX\SweetAlert\Enum\Position;
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

class InputModal
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public string $title = '';

    #[LiveProp]
    public string $text = '';

    #[LiveProp]
    public string $icon = '';

    #[LiveProp]
    public string $callback = '';

    #[LiveProp]
    public string $customClass = '';

    #[LiveProp]
    public string $confirmButtonText = 'OK';

    #[LiveProp]
    public string $cancelButtonText = 'Cancel';

    #[LiveProp]
    public bool $showCancelButton = true;

    #[ExposeInTemplate(getter: 'isDisabled')]
    public bool $disabled = false;

    #[LiveProp]
    public string $inputType = InputType::Text->value;

    #[LiveProp]
    public string $inputLabel = '';

    #[LiveProp]
    public string $inputPlaceholder = '';

    #[LiveProp]
    public string $inputValue = '';

    #[LiveProp]
    public string $inputOptions = '';

    #[LiveProp]
    public string $inputAttributes = '';

    #[LiveProp]
    public string $validationMessage = '';

    #[LiveProp]
    public bool $returnInputValueOnDeny = false;

    protected ?Result $result = null;

    protected ?TranslatorInterface $translator = null;

    private SweetAlertContextInterface $context;

    #[LiveListener('alertAdded')]
    public function alertAdded(): void
    {
        $alert = Alert::new(
            title: $this->translate($this->title),
            text: $this->translate($this->text),
            icon: !empty($this->icon) ? Icon::from($this->icon) : null,
            position: Position::CENTER,
            customClass: $this->customClass()
        );

        $alert
            ->confirmButtonText($this->translate($this->confirmButtonText))
            ->cancelButtonText($this->translate($this->cancelButtonText));

        if ($this->showCancelButton) {
            $alert->withCancelButton();
        }

        $alert->input($this->inputType);

        if (!empty($this->inputLabel)) {
            $alert->inputLabel($this->inputLabel);
        }

        if (!empty($this->inputPlaceholder)) {
            $alert->inputPlaceholder($this->inputPlaceholder);
        }

        if (!empty($this->inputValue)) {
            $alert->inputValue($this->inputValue);
        }

        if (!empty($this->inputOptions)) {
            $options = json_decode($this->inputOptions, true);
            if (is_array($options)) {
                $alert->inputOptions($options);
            }
        }

        if (!empty($this->inputAttributes)) {
            $attrs = json_decode($this->inputAttributes, true);
            if (is_array($attrs)) {
                $alert->inputAttributes($attrs);
            }
        }

        if (!empty($this->validationMessage)) {
            $alert->validationMessage($this->validationMessage);
        }

        if ($this->returnInputValueOnDeny) {
            $alert->returnInputValueOnDeny();
        }

        $this->context->addAlert($alert);

        $this->dispatchBrowserEvent('ux-sweet-alert:alert:added', [
            'alert'    => $alert,
            'callback' => $this->callback,
        ]);
    }

    #[LiveAction]
    public function callbackAction(#[LiveArg] array $result, #[LiveArg] array $args = []): mixed
    {
        $this->result = Result::fromArray($result);

        $this->onResult($this->result, $args);

        $this->dispatchBrowserEvent('ux-sweet-alert:callback', [
            'result' => $result,
            'args'   => $args,
        ]);

        return null;
    }

    protected function onResult(Result $result, array $args = []): void
    {

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
        if (empty($this->customClass)) {
            return [];
        }

        $customClass = json_decode($this->customClass, true);

        return \is_array($customClass) ? $customClass : [];
    }

    private function translate(string $message): string
    {
        return $this->translator?->trans($message) ?? $message;
    }
}
