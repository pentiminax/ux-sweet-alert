<?php

namespace Pentiminax\UX\SweetAlert\Twig\Components;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Model\Result;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
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
    public string $icon = Icon::SUCCESS->value;

    #[LiveProp]
    public string $callback = '';

    #[LiveProp]
    public string $customClass = '';

    #[LiveProp]
    public string $confirmButtonText = 'OK';

    #[LiveProp]
    public string $cancelButtonText = 'Cancel';

    protected ?Result $result = null;

    #[LiveListener('alertAdded')]
    public function alertAdded(): void
    {
        $this->dispatchBrowserEvent('ux-sweet-alert:alert:added', [
            'alert' => [
                'title' => $this->title,
                'text' => $this->text,
                'icon' => $this->icon,
                'showCancelButton' => $this->showCancelButton,
                'customClass' => $this->customClass(),
                'confirmButtonText' => $this->confirmButtonText,
                'cancelButtonText' => $this->cancelButtonText
            ],
            'callback' => $this->callback
        ]);
    }

    #[LiveAction]
    public function callbackAction(#[LiveArg] array $result, #[LiveArg] array $args = []): void
    {
        $this->result = Result::fromArray($result);
    }

    private function customClass(): array
    {
        return empty($this->customClass) ? [] : json_decode($this->customClass, true);
    }
}