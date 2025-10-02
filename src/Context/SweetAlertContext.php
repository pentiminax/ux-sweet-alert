<?php

namespace Pentiminax\UX\SweetAlert\Context;

use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\Toast;
use Symfony\Contracts\Service\ResetInterface;

class SweetAlertContext implements ResetInterface, SweetAlertContextInterface
{
    /** @var Alert[] */
    private array $alerts = [];

    /** @var Toast[] */
    private array $toasts = [];

    public function addAlert(Alert $alert): void
    {
        $this->alerts[] = $alert;
    }

    public function getAlerts(): array
    {
        return $this->alerts;
    }

    public function addToast(Toast $toast): void
    {
        $this->toasts[] = $toast;
    }

    public function getToasts(): array
    {
        return $this->toasts;
    }


    public function reset(): void
    {
        $this->alerts = [];
        $this->toasts = [];
    }
}