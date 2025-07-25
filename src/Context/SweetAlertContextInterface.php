<?php

namespace Pentiminax\UX\SweetAlert\Context;

use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\Toast;

interface SweetAlertContextInterface
{
    public function addAlert(Alert $alert): void;

    /**
     * @return Alert[]
     */
    public function getAlerts(): array;

    public function addToast(Toast $toast): void;

    /**
     * @return Toast[]
     */
    public function getToasts(): array;
}