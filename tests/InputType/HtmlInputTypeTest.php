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
use PHPUnit\Framework\TestCase;

class HtmlInputTypeTest extends TestCase
{
    public function testConfigureEmail(): void
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

    public function testConfigurePassword(): void
    {
        $alert = Alert::new('Test');
        $input = new HtmlInputType(InputType::Password, 'Password');

        $input->configure($alert);

        $this->assertSame('password', $alert->jsonSerialize()['input']);
    }

    public function testConfigureNumber(): void
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

    public function testRejectsSelectType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(Select::class);

        new HtmlInputType(InputType::Select);
    }

    public function testRejectsRadioType(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new HtmlInputType(InputType::Radio);
    }

    public function testRejectsCheckboxType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(Checkbox::class);

        new HtmlInputType(InputType::Checkbox);
    }

    public function testRejectsFileType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(File::class);

        new HtmlInputType(InputType::File);
    }

    public function testRejectsRangeType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(Range::class);

        new HtmlInputType(InputType::Range);
    }

    public function testRejectsTextareaType(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(Textarea::class);

        new HtmlInputType(InputType::Textarea);
    }
}
