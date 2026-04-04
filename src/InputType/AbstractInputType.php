<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\InputType;

use Pentiminax\UX\SweetAlert\Enum\InputType;
use Pentiminax\UX\SweetAlert\Model\Alert;

abstract class AbstractInputType implements InputTypeInterface
{
    public function __construct(
        protected readonly InputType $type,
        protected readonly ?string $label = null,
        protected readonly ?string $value = null,
        protected readonly ?string $placeholder = null,
        protected readonly array $inputAttributes = [],
    ) {}

    public function configure(Alert $alert): void
    {
        $alert->input($this->type->value);

        if (null !== $this->label) {
            $alert->inputLabel($this->label);
        }

        if (null !== $this->value) {
            $alert->inputValue($this->value);
        }

        if (null !== $this->placeholder && $this->type->supportsPlaceholder()) {
            $alert->inputPlaceholder($this->placeholder);
        }

        if (!empty($this->inputAttributes)) {
            $alert->inputAttributes($this->inputAttributes);
        }
    }
}
