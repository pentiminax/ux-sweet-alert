<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Model;

use Pentiminax\UX\SweetAlert\Enum\Animation;
use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\AlertDefaults;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(Alert::class)]
final class AlertTest extends TestCase
{
    #[Test]
    public function it_creates_alert_with_all_fluent_options(): void
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
            ->cancelButtonColor('#00ff00')
            ->denyButtonColor('#0000ff')
            ->reverseButtons()
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

        $this->assertSame('id', $data['id']);
        $this->assertSame('title', $data['title']);
        $this->assertSame('text', $data['text']);
        $this->assertSame(Icon::INFO->value, $data['icon']);
        $this->assertFalse($data['showConfirmButton']);
        $this->assertTrue($data['showCancelButton']);
        $this->assertTrue($data['showDenyButton']);
        $this->assertFalse($data['animation']);
        $this->assertSame(Theme::Dark->value, $data['theme']);
        $this->assertFalse($data['backdrop']);
        $this->assertFalse($data['allowOutsideClick']);
        $this->assertFalse($data['allowEscapeKey']);
        $this->assertSame('#ff0000', $data['confirmButtonColor']);
        $this->assertSame('#00ff00', $data['cancelButtonColor']);
        $this->assertSame('#0000ff', $data['denyButtonColor']);
        $this->assertTrue($data['reverseButtons']);
        $this->assertSame(Position::CENTER->value, $data['position']);
        $this->assertSame(['confirmButton' => 'btn btn-success'], $data['customClass']);
        $this->assertSame('<b>html</b>', $data['html']);
        $this->assertArrayNotHasKey('toast', $data);
    }

    #[Test]
    public function it_serializes_bootstrap_theme(): void
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

        $this->assertSame(Theme::Bootstrap5->value, $data['theme']);
    }

    #[Test]
    public function it_configures_toast_mode_with_timer_and_progress_bar(): void
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
        $this->assertArrayNotHasKey('draggable', $data);
        $this->assertArrayNotHasKey('focusConfirm', $data);
    }

    #[Test]
    public function it_serializes_timer_but_not_toast_flag_for_standard_alerts(): void
    {
        $alert = Alert::new(
            title: 'standard',
            id: 'alert-id',
            text: 'text'
        );

        $data = $alert->jsonSerialize();

        $this->assertFalse($alert->isToast());
        $this->assertArrayNotHasKey('toast', $data);
        $this->assertNull($data['timer']);
        $this->assertFalse($data['timerProgressBar']);
        $this->assertArrayHasKey('backdrop', $data);
        $this->assertArrayHasKey('allowOutsideClick', $data);
    }

    #[Test]
    public function it_applies_all_defaults_from_alert_defaults(): void
    {
        $defaults = new AlertDefaults(
            position: Position::TOP_END,
            theme: Theme::Dark,
            confirmButtonColor: '#ff0000',
            cancelButtonColor: '#00ff00',
            denyButtonColor: '#0000ff',
            confirmButtonText: 'Confirmer',
            cancelButtonText: 'Annuler',
            denyButtonText: 'Non',
            showConfirmButton: true,
            showCancelButton: true,
            showDenyButton: true,
            reverseButtons: true,
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
        $this->assertSame('#00ff00', $data['cancelButtonColor']);
        $this->assertSame('#0000ff', $data['denyButtonColor']);
        $this->assertSame('Confirmer', $data['confirmButtonText']);
        $this->assertSame('Annuler', $data['cancelButtonText']);
        $this->assertTrue($data['showConfirmButton']);
        $this->assertTrue($data['showCancelButton']);
        $this->assertTrue($data['showDenyButton']);
        $this->assertTrue($data['reverseButtons']);
        $this->assertFalse($data['backdrop']);
        $this->assertSame(['popup' => 'my-popup'], $data['customClass']);
        $this->assertFalse($data['animation']);
        $this->assertFalse($data['allowOutsideClick']);
        $this->assertFalse($data['allowEscapeKey']);
        $this->assertFalse($data['focusConfirm']);
        $this->assertTrue($data['draggable']);
        $this->assertTrue($data['topLayer']);
    }

    #[Test]
    public function it_overrides_default_position(): void
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

    #[Test]
    public function it_overrides_default_custom_class(): void
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

    #[Test]
    public function it_allows_fluent_methods_to_override_defaults(): void
    {
        $defaults = new AlertDefaults(
            confirmButtonColor: '#ff0000',
            confirmButtonText: 'Confirmer',
            showCancelButton: false,
            reverseButtons: false,
        );

        $alert = Alert::withDefaults(
            defaults: $defaults,
            title: 'Test',
        );

        $alert
            ->confirmButtonColor('#00ff00')
            ->confirmButtonText('Override')
            ->withCancelButton()
            ->reverseButtons(true);

        $data = $alert->jsonSerialize();

        $this->assertSame('#00ff00', $data['confirmButtonColor']);
        $this->assertSame('Override', $data['confirmButtonText']);
        $this->assertTrue($data['showCancelButton']);
        $this->assertTrue($data['reverseButtons']);
    }

    #[Test]
    public function it_uses_auto_theme_when_defaults_theme_is_null(): void
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

    #[Test]
    public function it_includes_callback_url_in_serialized_output(): void
    {
        $alert = Alert::new(title: 'Test');
        $alert->callbackUrl('/api/callback');

        $data = $alert->jsonSerialize();

        $this->assertSame('/api/callback', $data['callbackUrl']);
    }

    #[Test]
    public function it_omits_callback_url_from_serialized_output_when_not_set(): void
    {
        $alert = Alert::new(title: 'Test');

        $data = $alert->jsonSerialize();

        $this->assertArrayNotHasKey('callbackUrl', $data);
    }

    #[Test]
    public function it_serializes_pre_confirm_url_and_error_message_only_when_set(): void
    {
        $this->assertArrayNotHasKey('preConfirmUrl', Alert::new(title: 'Test')->jsonSerialize());

        $data = Alert::new(title: 'Test')->preConfirmUrl('/check', 'Oops')->jsonSerialize();

        $this->assertSame('/check', $data['preConfirmUrl']);
        $this->assertSame('Oops', $data['preConfirmErrorMessage']);
        $this->assertArrayNotHasKey('preConfirmErrorMessage', Alert::new(title: 'Test')->preConfirmUrl('/check')->jsonSerialize());
    }

    #[Test]
    public function it_serializes_visual_options_only_when_set(): void
    {
        $data = Alert::new(title: 'Test')
            ->width('32rem')
            ->padding(0)
            ->background('#fff url(/images/trees.png)')
            ->color('#716add')
            ->setImageWidth(400)
            ->showClass(Animation::FadeInUp)
            ->hideClass('my-hide')
            ->jsonSerialize();

        $this->assertSame('32rem', $data['width']);
        $this->assertSame(0, $data['padding']);
        $this->assertSame('#fff url(/images/trees.png)', $data['background']);
        $this->assertSame('#716add', $data['color']);
        $this->assertSame(400, $data['imageWidth']);
        $this->assertSame(['popup' => 'animate__animated animate__fadeInUp animate__faster'], $data['showClass']);
        $this->assertSame(['popup' => 'my-hide'], $data['hideClass']);

        $defaults = Alert::new(title: 'Test')->jsonSerialize();
        foreach (['width', 'padding', 'background', 'color', 'imageWidth', 'showClass', 'hideClass'] as $key) {
            $this->assertArrayNotHasKey($key, $defaults);
        }
    }

    #[Test]
    public function it_keeps_visual_options_for_toasts(): void
    {
        $data = Alert::new(title: 'Test')->asToast()->showClass(Animation::FadeInDown)->width(300)->jsonSerialize();

        $this->assertSame(['popup' => Animation::FadeInDown->value], $data['showClass']);
        $this->assertSame(300, $data['width']);
    }

    #[Test]
    public function it_serializes_progress_steps_only_when_set(): void
    {
        $data = Alert::new(title: 'Test')
            ->progressSteps(['Account', 'Profile'])
            ->currentProgressStep(1)
            ->wizard('w1')
            ->jsonSerialize();

        $this->assertSame(['Account', 'Profile'], $data['progressSteps']);
        $this->assertSame(1, $data['currentProgressStep']);
        $this->assertSame('w1', $data['wizardId']);

        $defaults = Alert::new(title: 'Test')->jsonSerialize();
        foreach (['progressSteps', 'currentProgressStep', 'wizardId'] as $key) {
            $this->assertArrayNotHasKey($key, $defaults);
        }
    }
}
