<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\InputType;

use Pentiminax\UX\SweetAlert\Enum\InputType;

final class File extends AbstractInputType
{
    public function __construct(
        ?string $label = null,
        ?string $accept = null,
        array $inputAttributes = [],
    ) {
        if (null !== $accept) {
            $inputAttributes['accept'] = $accept;
        }

        parent::__construct(InputType::File, $label, null, null, $inputAttributes);
    }
}
