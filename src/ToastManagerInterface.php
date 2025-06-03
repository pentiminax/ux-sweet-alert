<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Toast;

interface ToastManagerInterface
{
    public function addToast(Toast $toast): void;

    public function success(
        string $id,
        string $title,
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null
    ): Toast;

    public function error(
        string $id,
        string $title,
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null
    ): Toast;

    public function warning(
        string $id,
        string $title,
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null
    ): Toast;

    public function info(
        string $id,
        string $title,
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null
    ): Toast;

    public function question(
        string $id,
        string $title,
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null
    ): Toast;

    /**
     * @return Toast[]
     */
    public function getToasts(): array;
}