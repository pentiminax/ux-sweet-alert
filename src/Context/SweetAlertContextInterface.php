<?php

namespace Pentiminax\UX\SweetAlert\Context;

use Pentiminax\UX\SweetAlert\Model\Alert;

interface SweetAlertContextInterface
{
    public function addAlert(Alert $alert): void;

    /**
     * @return Alert[]
     */
    public function getAlerts(): array;

    /**
     * Returns only alerts that are not toasts.
     *
     * @return Alert[]
     */
    public function getStandardAlerts(): array;

    /**
     * Returns only alerts that are toasts.
     *
     * @return Alert[]
     */
    public function getToasts(): array;
}
