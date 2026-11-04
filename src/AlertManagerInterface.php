<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\InputType\InputTypeInterface;
use Pentiminax\UX\SweetAlert\Model\Alert;

interface AlertManagerInterface
{
    public const ALERT_STORAGE_KEY = 'ux-sweet-alert:alerts';

    public function addAlert(Alert $alert): void;

    /**
     * Queues the alerts as a wizard: each shows the progress steps ('1'..'n' unless the alert sets its own),
     * they fire in order whatever the transport, and dismissing a step skips the remaining ones.
     */
    public function steps(Alert ...$alerts): void;

    /**
     * @return Alert[]
     */
    public function getAlerts(): array;

    public function success(
        string $title,
        string $id = '',
        string $text = '',
        ?Position $position = null,
        ?Theme $theme = null,
        ?array $customClass = null,
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert;

    public function error(
        string $title,
        string $id = '',
        string $text = '',
        ?Position $position = null,
        ?Theme $theme = null,
        ?array $customClass = null,
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert;

    public function warning(
        string $title,
        string $id = '',
        string $text = '',
        ?Position $position = null,
        ?Theme $theme = null,
        ?array $customClass = null,
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert;

    public function info(
        string $title,
        string $id = '',
        string $text = '',
        ?Position $position = null,
        ?Theme $theme = null,
        ?array $customClass = null,
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert;

    public function question(
        string $title,
        string $id = '',
        string $text = '',
        ?Position $position = null,
        ?Theme $theme = null,
        ?array $customClass = null,
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert;

    public function toast(
        string $title,
        string $id = '',
        string $text = '',
        ?Icon $icon = Icon::SUCCESS,
        ?Position $position = null,
        ?Theme $theme = null,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert;

    public function input(
        InputTypeInterface $inputType,
        string $title,
        string $id = '',
        string $text = '',
        ?Icon $icon = null,
        ?Position $position = null,
        ?Theme $theme = null,
        ?array $customClass = null,
        string $callback = '',
    ): Alert;
}
