<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Enum;

use Pentiminax\UX\SweetAlert\Enum\InputType;
use PHPUnit\Framework\TestCase;

class InputTypeTest extends TestCase
{
    public function testAllEighteenTypesExist(): void
    {
        $expectedValues = [
            'text', 'email', 'password', 'number', 'tel', 'url', 'search',
            'date', 'datetime-local', 'time', 'week', 'month',
            'textarea', 'range', 'select', 'radio', 'checkbox', 'file',
        ];

        $actualValues = array_map(fn(InputType $t) => $t->value, InputType::cases());

        sort($expectedValues);
        sort($actualValues);

        $this->assertSame($expectedValues, $actualValues);
    }

    public function testSupportsInputOptions(): void
    {
        $this->assertTrue(InputType::Select->supportsInputOptions());
        $this->assertTrue(InputType::Radio->supportsInputOptions());

        $this->assertFalse(InputType::Text->supportsInputOptions());
        $this->assertFalse(InputType::Checkbox->supportsInputOptions());
        $this->assertFalse(InputType::File->supportsInputOptions());
        $this->assertFalse(InputType::Textarea->supportsInputOptions());
    }

    public function testSupportsPlaceholder(): void
    {
        $this->assertTrue(InputType::Text->supportsPlaceholder());
        $this->assertTrue(InputType::Email->supportsPlaceholder());
        $this->assertTrue(InputType::Password->supportsPlaceholder());
        $this->assertTrue(InputType::Textarea->supportsPlaceholder());

        $this->assertFalse(InputType::Select->supportsPlaceholder());
        $this->assertFalse(InputType::Checkbox->supportsPlaceholder());
        $this->assertFalse(InputType::File->supportsPlaceholder());
        $this->assertFalse(InputType::Range->supportsPlaceholder());
    }
}
