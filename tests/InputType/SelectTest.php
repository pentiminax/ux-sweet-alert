<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\Select;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Select::class)]
final class SelectTest extends TestCase
{
    #[Test]
    public function it_configures_alert_with_flat_options(): void
    {
        $alert = Alert::new('Test');
        $input = new Select(
            options: ['cat' => 'Cat', 'dog' => 'Dog', 'bird' => 'Bird'],
            label: 'Choose a pet',
            value: 'dog',
        );

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('select', $data['input']);
        $this->assertSame('Choose a pet', $data['inputLabel']);
        $this->assertSame('dog', $data['inputValue']);
        $this->assertSame(['cat' => 'Cat', 'dog' => 'Dog', 'bird' => 'Bird'], $data['inputOptions']);
    }

    #[Test]
    public function it_configures_alert_with_optgroup_options(): void
    {
        $alert = Alert::new('Test');
        $input = new Select(
            options: [
                'Fruits'     => ['apple' => 'Apple', 'banana' => 'Banana'],
                'Vegetables' => ['potato' => 'Potato', 'carrot' => 'Carrot'],
            ],
            label: 'Choose food',
        );

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('select', $data['input']);
        $this->assertArrayHasKey('Fruits', $data['inputOptions']);
        $this->assertSame(['apple' => 'Apple', 'banana' => 'Banana'], $data['inputOptions']['Fruits']);
    }

    #[Test]
    public function it_sets_input_options_to_null_when_no_options_provided(): void
    {
        $alert = Alert::new('Test');
        $input = new Select(label: 'Choose');

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('select', $data['input']);
        $this->assertNull($data['inputOptions']);
    }
}
