<?php

namespace Pentiminax\UX\SweetAlert\Tests\Model;

use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\Model\Toast;
use PHPUnit\Framework\TestCase;

class ToastTest extends TestCase
{
    public function testSerializeSupportsMaterialTheme(): void
    {
        $toast = Toast::new(
            title: 'toast',
            id: 'toast-id',
            text: 'text'
        );

        $toast->theme(Theme::MaterialUIDark);

        $data = $toast->jsonSerialize();

        $this->assertSame(Theme::MaterialUIDark->value, $data['theme']);
        $this->assertTrue($data['toast']);
        $this->assertArrayNotHasKey('backdrop', $data);
    }
}
