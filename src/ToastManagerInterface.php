<?php

namespace Pentiminax\UX\SweetAlert;

interface ToastManagerInterface
{
    public function addToast(string $title, string $text, string $icon): void;

    public function getToasts(): array;

    public function success(string $title, string $text): void;
}