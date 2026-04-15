<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\InputType;

use Pentiminax\UX\SweetAlert\Enum\InputType;

final class HtmlInputType extends AbstractInputType
{
    /**
     * @var array<string, class-string>
     */
    private const array SPECIALIZED_CLASSES = [
        InputType::Select->value   => Select::class,
        InputType::Radio->value    => Radio::class,
        InputType::Checkbox->value => Checkbox::class,
        InputType::File->value     => File::class,
        InputType::Range->value    => Range::class,
        InputType::Textarea->value => Textarea::class,
    ];

    public function __construct(
        InputType $type,
        ?string $label = null,
        ?string $value = null,
        ?string $placeholder = null,
        array $inputAttributes = [],
    ) {
        if (isset(self::SPECIALIZED_CLASSES[$type->value])) {
            throw new \InvalidArgumentException(\sprintf('Input type "%s" requires a specialized class. Use %s instead.', $type->value, self::SPECIALIZED_CLASSES[$type->value]));
        }

        parent::__construct($type, $label, $value, $placeholder, $inputAttributes);
    }
}
