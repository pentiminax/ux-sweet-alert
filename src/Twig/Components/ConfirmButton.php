<?php

namespace Pentiminax\UX\SweetAlert\Twig\Components;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\Result;
use Symfony\Contracts\Service\Attribute\Required;
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

    private readonly SweetAlertContextInterface $context;

    #[LiveListener('alertAdded')]
    public function alertAdded(): void
    {
        $alert = Alert::new(
            id: uniqid(),
            title: $this->title,
            text: $this->text,
            icon: Icon::from($this->icon),
            position: Position::CENTER,
            customClass: $this->customClass()
        );

        $alert
            ->confirmButtonText($this->confirmButtonText)
            ->cancelButtonText($this->cancelButtonText);

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
    public function callbackAction(#[LiveArg] array $result, #[LiveArg] array $args = []): void
    {
        $this->result = Result::fromArray($result);
    }

    #[Required]
    public function setContext(SweetAlertContextInterface $context): void
    {
        $this->context = $context;
    }

    private function customClass(): array
    {
        return empty($this->customClass) ? [] : json_decode($this->customClass, true);
    }
}