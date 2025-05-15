<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Toast;

interface ToastManagerInterface
{
    public function addToast(Toast $toast): void;

    public function success(string $id, string $title, string $text, Position $position, bool $showConfirmButton, ?int $timer = null): void;

    public function error(string $id, string $title, string $text, Position $position, bool $showConfirmButton, ?int $timer = null): void;

    public function warning(string $id, string $title, string $text, Position $position, bool $showConfirmButton, ?int $timer = null): void;

    public function info(string $id, string $title, string $text, Position $position, bool $showConfirmButton, ?int $timer = null): void;

    public function question(string $id, string $title, string $text, Position $position, bool $showConfirmButton, ?int $timer = null): Toast;
}