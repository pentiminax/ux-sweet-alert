<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\InputType;

use Pentiminax\UX\SweetAlert\Enum\InputType;
use Pentiminax\UX\SweetAlert\Model\Alert;

final class Select extends AbstractInputType
{
    public function __construct(
        private readonly array $options = [],
        ?string $label = null,
        ?string $value = null,
        array $inputAttributes = [],
    ) {
        parent::__construct(InputType::Select, $label, $value, null, $inputAttributes);
    }

    public function configure(Alert $alert): void
    {
        parent::configure($alert);

        if (!empty($this->options)) {
            $alert->inputOptions($this->options);
        }
    }
}
