<?php

namespace Pentiminax\UX\SweetAlert\Tests\Model;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\AlertDefaults;
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
                'confirmButton' => 'btn btn-success',
            ]
        );

        $alert
            ->confirmButtonColor('#ff0000')
            ->denyEscapeKey()
            ->denyOutsideClick()
            ->theme(Theme::Dark)
            ->withCancelButton()
            ->withDenyButton()
            ->withoutAnimation()
            ->withoutBackdrop()
            ->withoutConfirmButton()
            ->html('<b>html</b>')
        ;

        $data = $alert->jsonSerialize();

        $this->assertEquals('id', $data['id']);
        $this->assertEquals('title', $data['title']);
        $this->assertEquals('text', $data['text']);
        $this->assertEquals(Icon::INFO->value, $data['icon']);
        $this->assertFalse($data['showConfirmButton']);
        $this->assertTrue($data['showCancelButton']);
        $this->assertTrue($data['showDenyButton']);
        $this->assertFalse($data['animation']);
        $this->assertEquals(Theme::Dark->value, $data['theme']);
        $this->assertFalse($data['backdrop']);
        $this->assertTrue($data['allowOutsideClick']);
        $this->assertTrue($data['allowEscapeKey']);
        $this->assertEquals('#ff0000', $data['confirmButtonColor']);
        $this->assertEquals(Position::CENTER->value, $data['position']);
        $this->assertEquals(['confirmButton' => 'btn btn-success'], $data['customClass']);
        $this->assertEquals('<b>html</b>', $data['html']);
        $this->assertArrayNotHasKey('toast', $data);
    }

    public function testSerializeSupportsBootstrapTheme(): void
    {
        $alert = Alert::new(
            title: 'title',
            id: 'bootstrap-id',
            text: 'text',
            icon: Icon::INFO,
            position: Position::CENTER,
            customClass: []
        );

        $alert->theme(Theme::Bootstrap5);

        $data = $alert->jsonSerialize();

        $this->assertEquals(Theme::Bootstrap5->value, $data['theme']);
    }

    public function testToastMode(): void
    {
        $alert = Alert::new(
            title: 'toast',
            id: 'toast-id',
            text: 'text'
        );

        $alert
            ->asToast()
            ->timer(3000)
            ->withTimerProgressBar()
            ->theme(Theme::MaterialUIDark);

        $data = $alert->jsonSerialize();

        $this->assertTrue($alert->isToast());
        $this->assertSame(Theme::MaterialUIDark->value, $data['theme']);
        $this->assertTrue($data['toast']);
        $this->assertSame(3000, $data['timer']);
        $this->assertTrue($data['timerProgressBar']);
        $this->assertArrayNotHasKey('backdrop', $data);
        $this->assertArrayNotHasKey('allowOutsideClick', $data);
    }

    public function testStandardAlertDoesNotIncludeToastFields(): void
    {
        $alert = Alert::new(
            title: 'standard',
            id: 'alert-id',
            text: 'text'
        );

        $data = $alert->jsonSerialize();

        $this->assertFalse($alert->isToast());
        $this->assertArrayNotHasKey('toast', $data);
        $this->assertArrayNotHasKey('timer', $data);
        $this->assertArrayNotHasKey('timerProgressBar', $data);
        $this->assertArrayHasKey('backdrop', $data);
        $this->assertArrayHasKey('allowOutsideClick', $data);
    }

    public function testWithDefaultsUsesDefaults(): void
    {
        $defaults = new AlertDefaults(
            position: Position::TOP_END,
            theme: Theme::Dark,
            confirmButtonColor: '#ff0000',
            confirmButtonText: 'Confirmer',
            cancelButtonText: 'Annuler',
            denyButtonText: 'Non',
            showConfirmButton: true,
            showCancelButton: true,
            showDenyButton: true,
            backdrop: false,
            customClass: ['popup' => 'my-popup'],
            animation: false,
            allowOutsideClick: false,
            allowEscapeKey: false,
            focusConfirm: false,
            draggable: true,
            topLayer: true,
            timer: 3000,
            timerProgressBar: true,
        );

        $alert = Alert::withDefaults(
            defaults: $defaults,
            title: 'Test Title',
            id: 'test-id',
            text: 'Test Text',
            icon: Icon::INFO,
        );

        $data = $alert->jsonSerialize();

        $this->assertSame('test-id', $data['id']);
        $this->assertSame('Test Title', $data['title']);
        $this->assertSame('Test Text', $data['text']);
        $this->assertSame(Icon::INFO->value, $data['icon']);
        $this->assertSame(Position::TOP_END->value, $data['position']);
        $this->assertSame(Theme::Dark->value, $data['theme']);
        $this->assertSame('#ff0000', $data['confirmButtonColor']);
        $this->assertSame('Confirmer', $data['confirmButtonText']);
        $this->assertSame('Annuler', $data['cancelButtonText']);
        $this->assertTrue($data['showConfirmButton']);
        $this->assertTrue($data['showCancelButton']);
        $this->assertTrue($data['showDenyButton']);
        $this->assertFalse($data['backdrop']);
        $this->assertSame(['popup' => 'my-popup'], $data['customClass']);
        $this->assertFalse($data['animation']);
        $this->assertFalse($data['allowOutsideClick']);
        $this->assertFalse($data['allowEscapeKey']);
        $this->assertFalse($data['focusConfirm']);
        $this->assertTrue($data['draggable']);
        $this->assertTrue($data['topLayer']);
    }

    public function testWithDefaultsPositionCanBeOverridden(): void
    {
        $defaults = new AlertDefaults(
            position: Position::CENTER,
        );

        $alert = Alert::withDefaults(
            defaults: $defaults,
            title: 'Test',
            position: Position::TOP_END,
        );

        $this->assertSame(Position::TOP_END, $alert->getPosition());
    }

    public function testWithDefaultsCustomClassCanBeOverridden(): void
    {
        $defaults = new AlertDefaults(
            customClass: ['popup' => 'default-popup'],
        );

        $alert = Alert::withDefaults(
            defaults: $defaults,
            title: 'Test',
            customClass: ['popup' => 'custom-popup'],
        );

        $data = $alert->jsonSerialize();

        $this->assertSame(['popup' => 'custom-popup'], $data['customClass']);
    }

    public function testWithDefaultsFluentMethodsOverrideDefaults(): void
    {
        $defaults = new AlertDefaults(
            confirmButtonColor: '#ff0000',
            confirmButtonText: 'Confirmer',
            showCancelButton: false,
        );

        $alert = Alert::withDefaults(
            defaults: $defaults,
            title: 'Test',
        );

        // Override defaults using fluent methods
        $alert
            ->confirmButtonColor('#00ff00')
            ->confirmButtonText('Override')
            ->withCancelButton();

        $data = $alert->jsonSerialize();

        $this->assertSame('#00ff00', $data['confirmButtonColor']);
        $this->assertSame('Override', $data['confirmButtonText']);
        $this->assertTrue($data['showCancelButton']);
    }

    public function testWithDefaultsUsesAutoThemeWhenDefaultsThemeIsNull(): void
    {
        $defaults = new AlertDefaults(
            theme: null,
        );

        $alert = Alert::withDefaults(
            defaults: $defaults,
            title: 'Test',
        );

        $data = $alert->jsonSerialize();

        $this->assertSame(Theme::Auto->value, $data['theme']);
    }
}
