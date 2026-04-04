<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Enum;

use Pentiminax\UX\SweetAlert\Enum\InputType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(InputType::class)]
final class InputTypeTest extends TestCase
{
    #[Test]
    public function it_defines_all_eighteen_input_types(): void
    {
        $expectedValues = [
            'text', 'email', 'password', 'number', 'tel', 'url', 'search',
            'date', 'datetime-local', 'time', 'week', 'month',
            'textarea', 'range', 'select', 'radio', 'checkbox', 'file',
        ];

        $actualValues = array_map(fn (InputType $t) => $t->value, InputType::cases());

        sort($expectedValues);
        sort($actualValues);

        $this->assertSame($expectedValues, $actualValues);
    }

    #[Test]
    public function it_identifies_types_that_support_input_options(): void
    {
        $this->assertTrue(InputType::Select->supportsInputOptions());
        $this->assertTrue(InputType::Radio->supportsInputOptions());

        $this->assertFalse(InputType::Text->supportsInputOptions());
        $this->assertFalse(InputType::Checkbox->supportsInputOptions());
        $this->assertFalse(InputType::File->supportsInputOptions());
        $this->assertFalse(InputType::Textarea->supportsInputOptions());
    }

    #[Test]
    public function it_identifies_types_that_support_placeholder(): void
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
