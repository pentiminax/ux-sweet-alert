<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\InputType;

use Pentiminax\UX\SweetAlert\InputType\Textarea;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Textarea::class)]
final class TextareaTest extends TestCase
{
    #[Test]
    public function it_configures_alert_with_textarea_input(): void
    {
        $alert = Alert::new('Test');
        $input = new Textarea(
            label: 'Your message',
            placeholder: 'Write something...',
        );

        $input->configure($alert);
        $data = $alert->jsonSerialize();

        $this->assertSame('textarea', $data['input']);
        $this->assertSame('Your message', $data['inputLabel']);
        $this->assertSame('Write something...', $data['inputPlaceholder']);
    }
}
