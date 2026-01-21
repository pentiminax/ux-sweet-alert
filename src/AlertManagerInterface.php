<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\Model\Alert;

interface AlertManagerInterface
{
    public const ALERT_STORAGE_KEY = 'ux-sweet-alert:alerts';

    public function addAlert(Alert $alert): void;

    /**
     * @return Alert[]
     */
    public function getAlerts(): array;

    public function success(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::CENTER,
        ?Theme $theme = null,
        array $customClass = [],
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Alert;

    public function error(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::CENTER,
        ?Theme $theme = null,
        array $customClass = [],
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Alert;

    public function warning(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::CENTER,
        ?Theme $theme = null,
        array $customClass = [],
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Alert;

    public function info(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::CENTER,
        ?Theme $theme = null,
        array $customClass = [],
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Alert;

    public function question(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::CENTER,
        ?Theme $theme = null,
        array $customClass = [],
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Alert;

    public function toast(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::BOTTOM_END,
        ?Theme $theme = null,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Alert;
}
