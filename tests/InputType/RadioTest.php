<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\Radio;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class RadioTest extends TestCase
{
    public function test_configure_with_options(): void
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
