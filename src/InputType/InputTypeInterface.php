<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\InputType;

use Pentiminax\UX\SweetAlert\Model\Alert;

interface InputTypeInterface
{
    public function configure(Alert $alert): void;
}
