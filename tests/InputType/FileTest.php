<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\File;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testConfigureWithAccept(): void
    {
        $alert = Alert::new('Test');
        $input = new File(label: 'Upload image', accept: 'image/*');

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('file', $data['input']);
        $this->assertSame('Upload image', $data['inputLabel']);
        $this->assertSame(['accept' => 'image/*'], $data['inputAttributes']);
    }

    public function testConfigureWithoutAccept(): void
    {
        $alert = Alert::new('Test');
        $input = new File(label: 'Upload file');

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('file', $data['input']);
        $this->assertSame([], $data['inputAttributes']);
    }
}
