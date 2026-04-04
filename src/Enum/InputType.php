<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Enum;

enum InputType: string
{
    // Text-like HTML inputs
    case Text          = 'text';
    case Email         = 'email';
    case Password      = 'password';
    case Number        = 'number';
    case Tel           = 'tel';
    case Url           = 'url';
    case Search        = 'search';

    // Date/time HTML inputs
    case Date          = 'date';
    case DatetimeLocal = 'datetime-local';
    case Time          = 'time';
    case Week          = 'week';
    case Month         = 'month';

    // Special inputs with dedicated renderers
    case Textarea      = 'textarea';
    case Range         = 'range';
    case Select        = 'select';
    case Radio         = 'radio';
    case Checkbox      = 'checkbox';
    case File          = 'file';

    public function supportsInputOptions(): bool
    {
        return match ($this) {
            self::Select, self::Radio => true,
            default => false,
        };
    }

    public function supportsPlaceholder(): bool
    {
        return match ($this) {
            self::Select, self::Checkbox, self::File, self::Range => false,
            default => true,
        };
    }
}
