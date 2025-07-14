<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Alert;

interface AlertManagerInterface
{
    public function addAlert(Alert $alert): void;

    public function getAlerts(): array;

    public function success(string $id, string $title, string $text = '', Position $position = Position::CENTER, array $customClass = []): Alert;

    public function error(string $id, string $title, string $text = '', Position $position = Position::CENTER, array $customClass = []): Alert;

    public function warning(string $id, string $title, string $text = '', Position $position = Position::CENTER, array $customClass = []): Alert;

    public function info(string $id, string $title, string $text = '', Position $position = Position::CENTER, array $customClass = []): Alert;

    public function question(string $id, string $title, string $text = '', Position $position = Position::CENTER, array $customClass = []): Alert;
}