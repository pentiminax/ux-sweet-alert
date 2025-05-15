<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class AlertManager implements AlertManagerInterface
{
    private array $alerts = [];

    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    public function addAlert(Alert $alert): void
    {
        $this->alerts[] = $alert;

        $this->getSession()->getFlashBag()->set('ux-sweet-alert:alerts', $this->alerts);
    }

    public function getAlerts(): array
    {
        return $this->getSession()->getFlashBag()->get('ux-sweet-alert:alerts');
    }

    public function getSession(): FlashBagAwareSessionInterface
    {
        return $this->requestStack->getSession();
    }

    public function success(string $id, string $title, string $text, Position $position = Position::CENTER): Alert
    {
        return Alert::new($id, $title, $text, $position);
    }
}