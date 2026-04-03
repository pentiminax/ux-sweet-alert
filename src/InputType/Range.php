<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\InputType;

use Pentiminax\UX\SweetAlert\Enum\InputType;

final class Range extends AbstractInputType
{
    public function __construct(
        ?string $label = null,
        ?string $value = null,
        ?int $min = null,
        ?int $max = null,
        ?int $step = null,
        array $inputAttributes = [],
    ) {
        if (null !== $min) {
            $inputAttributes['min'] = (string) $min;
        }
        if (null !== $max) {
            $inputAttributes['max'] = (string) $max;
        }
        if (null !== $step) {
            $inputAttributes['step'] = (string) $step;
        }

        parent::__construct(InputType::Range, $label, $value, null, $inputAttributes);
    }
}
