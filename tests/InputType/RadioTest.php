<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\Radio;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Radio::class)]
final class RadioTest extends TestCase
{
    #[Test]
    public function it_configures_alert_with_radio_options(): void
    {
        $alert = Alert::new('Test');
        $input = new Radio(
            options: ['#ff0000' => 'Red', '#00ff00' => 'Green', '#0000ff' => 'Blue'],
            label: 'Pick a color',
        );

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('radio', $data['input']);
        $this->assertSame('Pick a color', $data['inputLabel']);
        $this->assertSame(['#ff0000' => 'Red', '#00ff00' => 'Green', '#0000ff' => 'Blue'], $data['inputOptions']);
    }
}
