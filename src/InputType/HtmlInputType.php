<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\InputType;

use Pentiminax\UX\SweetAlert\Enum\InputType;

final class HtmlInputType extends AbstractInputType
{
    private const SPECIALIZED_TYPES = [
        InputType::Select,
        InputType::Radio,
        InputType::Checkbox,
        InputType::File,
        InputType::Range,
        InputType::Textarea,
    ];

    public function __construct(
        InputType $type,
        ?string $label = null,
        ?string $value = null,
        ?string $placeholder = null,
        array $inputAttributes = [],
    ) {
        if (\in_array($type, self::SPECIALIZED_TYPES, true)) {
            throw new \InvalidArgumentException(\sprintf('Input type "%s" requires a specialized class. Use %s instead.', $type->value, match ($type) {
                InputType::Select => Select::class, InputType::Radio => Radio::class, InputType::Checkbox => Checkbox::class, InputType::File => File::class, InputType::Range => Range::class, InputType::Textarea => Textarea::class, default => 'a specialized InputType class',
            }));
        }

        parent::__construct($type, $label, $value, $placeholder, $inputAttributes);
    }
}
