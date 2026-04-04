<?php

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\Text;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public function testConfigure(): void
    {
        $alert = Alert::new('Test Alert');
        $textInput = new Text(
            label: 'Enter value',
            value: 'default value',
            placeholder: 'Wait...',
            validator: 'validator',
            inputAttributes: ['maxlength' => '10']
        );

        $textInput->configure($alert);

        $data = $alert->jsonSerialize();

        $this->assertEquals('text', $data['input']);
        $this->assertEquals('Enter value', $data['inputLabel']);
        $this->assertEquals('default value', $data['inputValue']);
        $this->assertEquals('Wait...', $data['inputPlaceholder']);
        $this->assertEquals('validator', $data['inputValidator']);
        $this->assertEquals(['maxlength' => '10'], $data['inputAttributes']);
    }
}
