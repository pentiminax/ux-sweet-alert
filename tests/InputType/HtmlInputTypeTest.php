<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\Enum\InputType;
use Pentiminax\UX\SweetAlert\InputType\Checkbox;
use Pentiminax\UX\SweetAlert\InputType\File;
use Pentiminax\UX\SweetAlert\InputType\HtmlInputType;
use Pentiminax\UX\SweetAlert\InputType\Range;
use Pentiminax\UX\SweetAlert\InputType\Select;
use Pentiminax\UX\SweetAlert\InputType\Textarea;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(HtmlInputType::class)]
final class HtmlInputTypeTest extends TestCase
{
    #[Test]
    public function it_configures_alert_with_email_input(): void
    {
        $alert = Alert::new('Test');
        $input = new HtmlInputType(
            type: InputType::Email,
            label: 'Email address',
            placeholder: 'you@example.com',
        );

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('email', $data['input']);
        $this->assertSame('Email address', $data['inputLabel']);
        $this->assertSame('you@example.com', $data['inputPlaceholder']);
    }

    #[Test]
    public function it_configures_alert_with_password_input(): void
    {
        $alert = Alert::new('Test');
        $input = new HtmlInputType(InputType::Password, 'Password');

        $input->configure($alert);

        $this->assertSame('password', $alert->jsonSerialize()['input']);
    }

    #[Test]
    public function it_configures_alert_with_number_input_and_attributes(): void
    {
        $alert = Alert::new('Test');
        $input = new HtmlInputType(
            type: InputType::Number,
            label: 'Age',
            inputAttributes: ['min' => '0', 'max' => '120'],
        );

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('number', $data['input']);
        $this->assertSame(['min' => '0', 'max' => '120'], $data['inputAttributes']);
    }

    #[Test]
    public function it_rejects_select_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(Select::class);

        new HtmlInputType(InputType::Select);
    }

    #[Test]
    public function it_rejects_radio_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new HtmlInputType(InputType::Radio);
    }

    #[Test]
    public function it_rejects_checkbox_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(Checkbox::class);

        new HtmlInputType(InputType::Checkbox);
    }

    #[Test]
    public function it_rejects_file_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(File::class);

        new HtmlInputType(InputType::File);
    }

    #[Test]
    public function it_rejects_range_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(Range::class);

        new HtmlInputType(InputType::Range);
    }

    #[Test]
    public function it_rejects_textarea_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(Textarea::class);

        new HtmlInputType(InputType::Textarea);
    }
}
