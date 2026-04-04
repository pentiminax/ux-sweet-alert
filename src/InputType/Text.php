<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\InputType;

use Pentiminax\UX\SweetAlert\Model\Alert;

final class Text implements InputTypeInterface
{
    private string $input = 'text';

    public function __construct(
        private readonly ?string $label = null,
        private readonly ?string $value = null,
        private readonly ?string $placeholder = null,
        private readonly ?string $validator = null,
        private readonly array $inputAttributes = [],
    ) {}

    public function configure(Alert $alert): void
    {
        $alert->input($this->input);

        if (null !== $this->label) {
            $alert->inputLabel($this->label);
        }

        if (null !== $this->value) {
            $alert->inputValue($this->value);
        }

        if (null !== $this->placeholder) {
            $alert->inputPlaceholder($this->placeholder);
        }

        if (null !== $this->validator) {
            $alert->inputValidator($this->validator);
        }

        if (!empty($this->inputAttributes)) {
            $alert->inputAttributes($this->inputAttributes);
        }
    }
}
