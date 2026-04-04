<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\Range;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Range::class)]
final class RangeTest extends TestCase
{
    #[Test]
    public function it_configures_alert_with_min_max_and_step_constraints(): void
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

    #[Test]
    public function it_configures_alert_without_constraints(): void
    {
        $alert = Alert::new('Test');
        $input = new Range(label: 'Slider');

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('range', $data['input']);
        $this->assertSame([], $data['inputAttributes']);
    }
}
