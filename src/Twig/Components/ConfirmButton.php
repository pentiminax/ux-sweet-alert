<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Twig\Components;

use Pentiminax\UX\SweetAlert\Enum\Icon;
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

class ConfirmButton
{
    use ComponentToolsTrait;
    use DefaultActionTrait;
    use ModalComponentTrait;

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

        $this->dispatchCallback($result, $args);

        return null;
    }
}
