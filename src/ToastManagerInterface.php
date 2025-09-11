<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Toast;

interface ToastManagerInterface
{
    public const TOAST_STORAGE_KEY = 'ux-sweet-alert:toasts';

    public function addToast(Toast $toast): void;

    public function success(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Toast;

    public function error(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Toast;

    public function warning(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Toast;

    public function info(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Toast;

    public function question(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Toast;

    /**
     * @return Toast[]
     */
    public function getToasts(): array;
}