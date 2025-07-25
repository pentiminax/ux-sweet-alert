<?php

namespace Pentiminax\UX\SweetAlert\Context;

use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\Toast;

class SweetAlertContext implements SweetAlertContextInterface
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
}