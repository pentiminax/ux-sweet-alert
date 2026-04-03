<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\InputType;

use Pentiminax\UX\SweetAlert\Enum\InputType;

final class Textarea extends AbstractInputType
{
    public function __construct(
        ?string $label = null,
        ?string $value = null,
        ?string $placeholder = null,
        array $inputAttributes = [],
    ) {
        parent::__construct(InputType::Textarea, $label, $value, $placeholder, $inputAttributes);
    }
}
