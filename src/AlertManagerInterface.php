<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Model\Alert;

interface AlertManagerInterface
{
    public function addAlert(Alert $alert): void;

    public function getAlerts(): array;

    public function success(string $id, string $title, string $text): Alert;
}