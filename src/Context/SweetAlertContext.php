<?php

namespace Pentiminax\UX\SweetAlert\Context;

use Pentiminax\UX\SweetAlert\Model\Alert;
use Symfony\Contracts\Service\ResetInterface;

class SweetAlertContext implements ResetInterface, SweetAlertContextInterface
{
    /** @var Alert[] */
    private array $alerts = [];

    public function addAlert(Alert $alert): void
    {
        $this->alerts[] = $alert;
    }

    public function getAlerts(): array
    {
        return $this->alerts;
    }

    public function getStandardAlerts(): array
    {
        return array_filter($this->alerts, fn(Alert $alert) => !$alert->isToast());
    }

    public function getToasts(): array
    {
        return array_filter($this->alerts, fn(Alert $alert) => $alert->isToast());
    }

    public function reset(): void
    {
        $this->alerts = [];
    }
}
