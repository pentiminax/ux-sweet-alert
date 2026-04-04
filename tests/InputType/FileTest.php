<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\File;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(File::class)]
final class FileTest extends TestCase
{
    #[Test]
    public function it_configures_alert_with_file_input_and_accept_attribute(): void
    {
        $alert = Alert::new('Test');
        $input = new File(label: 'Upload image', accept: 'image/*');

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('file', $data['input']);
        $this->assertSame('Upload image', $data['inputLabel']);
        $this->assertSame(['accept' => 'image/*'], $data['inputAttributes']);
    }

    #[Test]
    public function it_configures_alert_with_file_input_without_accept_attribute(): void
    {
        $alert = Alert::new('Test');
        $input = new File(label: 'Upload file');

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('file', $data['input']);
        $this->assertSame([], $data['inputAttributes']);
    }
}
