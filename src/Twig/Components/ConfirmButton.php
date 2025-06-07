<?php

namespace Pentiminax\UX\SweetAlert\Twig\Components;

use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\ComponentToolsTrait;
use Symfony\UX\LiveComponent\DefaultActionTrait;

class ConfirmButton
{
    use ComponentToolsTrait;
    use DefaultActionTrait;

    #[LiveProp]
    public string $title;

    #[LiveProp]
    public string $text;

    #[LiveProp]
    public bool $showCancelButton = false;

    #[LiveProp]
    public string $icon;

    #[LiveProp]
    public string $callback;

    #[LiveListener('alertAdded')]
    public function alertAdded(): void
    {
        $this->dispatchBrowserEvent('ux-sweet-alert:alert:added', [
            'alert' => [
                'title' => $this->title,
                'text' => $this->text,
                'icon' => $this->icon,
                'showCancelButton' => $this->showCancelButton,
            ],
            'callback' => $this->callback
        ]);
    }
}