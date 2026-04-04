<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Fixtures;

use Pentiminax\UX\SweetAlert\Model\Result;
use Pentiminax\UX\SweetAlert\Twig\Components\InputModal;

final class TestableInputModal extends InputModal
{
    public ?Result $receivedResult = null;

    public array $receivedArgs = [];

    protected function onResult(Result $result, array $args = []): void
    {
        $this->receivedResult = $result;
        $this->receivedArgs   = $args;
    }
}
