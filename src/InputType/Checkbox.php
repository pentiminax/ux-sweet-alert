<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\InputType;

use Pentiminax\UX\SweetAlert\Enum\InputType;

final class Checkbox extends AbstractInputType
{
    public function __construct(
        ?string $label = null,
        ?string $value = null,
        array $inputAttributes = [],
    ) {
        parent::__construct(InputType::Checkbox, $label, $value, null, $inputAttributes);
    }
}
