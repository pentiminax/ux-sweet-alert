<?php

namespace Pentiminax\UX\SweetAlert\Tests\Model;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\TestCase;

class AlertTest extends TestCase
{
    public function testCreateAlert(): void
    {
        $alert = Alert::new(
            title: 'title',
            id: 'id',
            text: 'text',
            icon: Icon::INFO,
            position: Position::CENTER,
            customClass: [
                'confirmButton' => 'btn btn-success'
            ]
        );

        $alert
            ->confirmButtonColor('#ff0000')
            ->denyEscapeKey()
            ->denyOutsideClick()
            ->theme(Theme::DARK)
            ->withCancelButton()
            ->withDenyButton()
            ->withoutAnimation()
            ->withoutBackdrop()
            ->withoutConfirmButton();

        $data = $alert->jsonSerialize();

        $this->assertEquals('id', $data['id']);
        $this->assertEquals('title', $data['title']);
        $this->assertEquals('text', $data['text']);
        $this->assertEquals(Icon::INFO->value, $data['icon']);
        $this->assertFalse($data['showConfirmButton']);
        $this->assertTrue($data['showCancelButton']);
        $this->assertTrue($data['showDenyButton']);
        $this->assertFalse($data['animation']);
        $this->assertEquals(Theme::DARK->value, $data['theme']);
        $this->assertFalse($data['backdrop']);
        $this->assertTrue($data['allowOutsideClick']);
        $this->assertTrue($data['allowEscapeKey']);
        $this->assertEquals('#ff0000', $data['confirmButtonColor']);
        $this->assertEquals(Position::CENTER->value, $data['position']);
        $this->assertEquals(['confirmButton' => 'btn btn-success'], $data['customClass']);
    }
}