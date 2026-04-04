<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\Text;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Text::class)]
final class TextTest extends TestCase
{
    #[Test]
    public function it_configures_alert_with_all_text_input_options(): void
    {
        $alert     = Alert::new('Test Alert');
        $textInput = new Text(
            label: 'Enter value',
            value: 'default value',
            placeholder: 'Wait...',
            inputAttributes: ['maxlength' => '10']
        );

        $textInput->configure($alert);

        $data = $alert->jsonSerialize();

        $this->assertSame('text', $data['input']);
        $this->assertSame('Enter value', $data['inputLabel']);
        $this->assertSame('default value', $data['inputValue']);
        $this->assertSame('Wait...', $data['inputPlaceholder']);
        $this->assertSame(['maxlength' => '10'], $data['inputAttributes']);
        $this->assertArrayNotHasKey('inputValidator', $data);
    }

    #[Test]
    public function it_configures_alert_with_minimal_text_input_options(): void
    {
        $alert     = Alert::new('Test Alert');
        $textInput = new Text(label: 'Enter value');

        $textInput->configure($alert);

        $data = $alert->jsonSerialize();

        $this->assertSame('text', $data['input']);
        $this->assertSame('Enter value', $data['inputLabel']);
        $this->assertNull($data['inputValue']);
        $this->assertNull($data['inputPlaceholder']);
        $this->assertSame([], $data['inputAttributes']);
    }
}
