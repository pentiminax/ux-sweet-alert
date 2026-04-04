<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\Checkbox;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Checkbox::class)]
final class CheckboxTest extends TestCase
{
    #[Test]
    public function it_configures_alert_with_checkbox_input(): void
    {
        $alert = Alert::new('Test');
        $input = new Checkbox(label: 'I agree to the terms');

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('checkbox', $data['input']);
        $this->assertSame('I agree to the terms', $data['inputLabel']);
    }
}
