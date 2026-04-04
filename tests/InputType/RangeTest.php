<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\Range;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\TestCase;

class RangeTest extends TestCase
{
    public function testConfigureWithMinMaxStep(): void
    {
        $alert = Alert::new('Test');
        $input = new Range(label: 'Volume', value: '50', min: 0, max: 100, step: 5);

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('range', $data['input']);
        $this->assertSame('Volume', $data['inputLabel']);
        $this->assertSame('50', $data['inputValue']);
        $this->assertSame(['min' => '0', 'max' => '100', 'step' => '5'], $data['inputAttributes']);
    }

    public function testConfigureWithoutConstraints(): void
    {
        $alert = Alert::new('Test');
        $input = new Range(label: 'Slider');

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('range', $data['input']);
        $this->assertSame([], $data['inputAttributes']);
    }
}
