<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Position;

interface ToastManagerInterface
{
    public function success(string $id, string $title, string $text, Position $position, bool $showConfirmButton): void;
}