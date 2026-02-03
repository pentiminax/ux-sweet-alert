<?php

namespace Pentiminax\UX\SweetAlert\Model;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;

final readonly class AlertDefaults
{
    public function __construct(
        public Position $position = Position::CENTER,
        public ?Theme $theme = null,
        public string $confirmButtonColor = '#3085d6',
        public string $cancelButtonColor = '#aaa',
        public string $denyButtonColor = '#dd6b55',
        public string $confirmButtonText = 'OK',
        public string $cancelButtonText = 'Cancel',
        public string $denyButtonText = 'No',
        public bool $showConfirmButton = true,
        public bool $showCancelButton = false,
        public bool $showDenyButton = false,
        public bool $reverseButtons = false,
        public bool $backdrop = true,
        public array $customClass = [],
        public bool $animation = true,
        public bool $allowOutsideClick = true,
        public bool $allowEscapeKey = true,
        public bool $focusConfirm = true,
        public bool $draggable = false,
        public bool $topLayer = false,
        public ?int $timer = null,
        public bool $timerProgressBar = false,
    ) {
    }

    public static function fromArray(array $config): self
    {
        return new self(
            position: isset($config['position']) ? Position::from($config['position']) : Position::CENTER,
            theme: isset($config['theme']) ? Theme::from($config['theme']) : Theme::Auto,
            confirmButtonColor: $config['confirmButtonColor'] ?? '#3085d6',
            cancelButtonColor: $config['cancelButtonColor'] ?? '#aaa',
            denyButtonColor: $config['denyButtonColor'] ?? '#dd6b55',
            confirmButtonText: $config['confirmButtonText']   ?? 'OK',
            cancelButtonText: $config['cancelButtonText']     ?? 'Cancel',
            denyButtonText: $config['denyButtonText']         ?? 'No',
            showConfirmButton: $config['showConfirmButton']   ?? true,
            showCancelButton: $config['showCancelButton']     ?? false,
            showDenyButton: $config['showDenyButton']         ?? false,
            reverseButtons: $config['reverseButtons']         ?? false,
            backdrop: $config['backdrop']                     ?? true,
            customClass: $config['customClass']               ?? [],
            animation: $config['animation']                   ?? true,
            allowOutsideClick: $config['allowOutsideClick']   ?? true,
            allowEscapeKey: $config['allowEscapeKey']         ?? true,
            focusConfirm: $config['focusConfirm']             ?? true,
            draggable: $config['draggable']                   ?? false,
            topLayer: $config['topLayer']                     ?? false,
            timer: $config['timer']                           ?? null,
            timerProgressBar: $config['timerProgressBar']     ?? false,
        );
    }
}
