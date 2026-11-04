<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Model;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;

final readonly class AlertDefaults
{
    public const CONFIRM_BUTTON_TEXT = 'OK';
    public const CANCEL_BUTTON_TEXT  = 'Cancel';
    public const DENY_BUTTON_TEXT    = 'No';

    public function __construct(
        public Position $position = Position::CENTER,
        public ?Theme $theme = null,
        // Null leaves the button colors to the SweetAlert2 theme.
        public ?string $confirmButtonColor = null,
        public ?string $cancelButtonColor = null,
        public ?string $denyButtonColor = null,
        public string $confirmButtonText = self::CONFIRM_BUTTON_TEXT,
        public string $cancelButtonText = self::CANCEL_BUTTON_TEXT,
        public string $denyButtonText = self::DENY_BUTTON_TEXT,
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
            confirmButtonColor: $config['confirmButtonColor'] ?? null,
            cancelButtonColor: $config['cancelButtonColor']   ?? null,
            denyButtonColor: $config['denyButtonColor']       ?? null,
            confirmButtonText: $config['confirmButtonText']   ?? self::CONFIRM_BUTTON_TEXT,
            cancelButtonText: $config['cancelButtonText']     ?? self::CANCEL_BUTTON_TEXT,
            denyButtonText: $config['denyButtonText']         ?? self::DENY_BUTTON_TEXT,
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
