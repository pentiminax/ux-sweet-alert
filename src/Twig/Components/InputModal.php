<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Twig\Components;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\InputType;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\Result;
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
    use ModalComponentTrait;

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

    #[LiveListener('alertAdded')]
    public function alertAdded(): void
    {
        $alert = Alert::new(
            title: $this->translate($this->title),
            text: $this->translate($this->text),
            icon: '' !== $this->icon ? Icon::from($this->icon) : null,
            position: Position::CENTER,
            customClass: $this->customClass()
        );

        $alert
            ->confirmButtonText($this->translate($this->confirmButtonText))
            ->cancelButtonText($this->translate($this->cancelButtonText));

        if ($this->showCancelButton) {
            $alert->withCancelButton();
        }

        $this->applyInput($alert);

        $this->queueAlert($alert);
    }

    /**
     * @param array<string, mixed> $result
     * @param array<string, mixed> $args
     */
    #[LiveAction]
    public function callbackAction(#[LiveArg] array $result, #[LiveArg] array $args = []): mixed
    {
        $this->result = Result::fromArray($result);

        $this->onResult($this->result, $args);

        $this->dispatchCallback($result, $args);

        return null;
    }

    protected function onResult(Result $result, array $args = []): void
    {
    }

    private function applyInput(Alert $alert): void
    {
        $alert->input($this->inputType);

        if ('' !== $this->inputLabel) {
            $alert->inputLabel($this->inputLabel);
        }

        if ('' !== $this->inputPlaceholder) {
            $alert->inputPlaceholder($this->inputPlaceholder);
        }

        // '0' is a legitimate value, so compare against '' instead of using empty().
        if ('' !== $this->inputValue) {
            $alert->inputValue($this->inputValue);
        }

        $alert->inputOptions($this->decodeJsonArray($this->inputOptions));
        $alert->inputAttributes($this->decodeJsonArray($this->inputAttributes));

        if ('' !== $this->validationMessage) {
            $alert->validationMessage($this->validationMessage);
        }

        if ($this->returnInputValueOnDeny) {
            $alert->returnInputValueOnDeny();
        }
    }
}
