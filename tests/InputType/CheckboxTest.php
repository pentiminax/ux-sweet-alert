<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\Checkbox;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class CheckboxTest extends TestCase
{
    public function test_configure(): void
    {
        $alert = Alert::new('Test');
        $input = new Checkbox(label: 'I agree to the terms');

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('checkbox', $data['input']);
        $this->assertSame('I agree to the terms', $data['inputLabel']);
    }
}
