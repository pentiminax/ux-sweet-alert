<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Model;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\Model\AlertDefaults;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class AlertDefaultsTest extends TestCase
{
    public function test_default_values(): void
    {
        $defaults = new AlertDefaults();

        $this->assertSame(Position::CENTER, $defaults->position);
        $this->assertNull($defaults->theme);
        $this->assertSame('#3085d6', $defaults->confirmButtonColor);
        $this->assertSame('#aaa', $defaults->cancelButtonColor);
        $this->assertSame('#dd6b55', $defaults->denyButtonColor);
        $this->assertSame('OK', $defaults->confirmButtonText);
        $this->assertSame('Cancel', $defaults->cancelButtonText);
        $this->assertSame('No', $defaults->denyButtonText);
        $this->assertTrue($defaults->showConfirmButton);
        $this->assertFalse($defaults->showCancelButton);
        $this->assertFalse($defaults->showDenyButton);
        $this->assertFalse($defaults->reverseButtons);
        $this->assertTrue($defaults->backdrop);
        $this->assertSame([], $defaults->customClass);
        $this->assertTrue($defaults->animation);
        $this->assertTrue($defaults->allowOutsideClick);
        $this->assertTrue($defaults->allowEscapeKey);
        $this->assertTrue($defaults->focusConfirm);
        $this->assertFalse($defaults->draggable);
        $this->assertFalse($defaults->topLayer);
        $this->assertNull($defaults->timer);
        $this->assertFalse($defaults->timerProgressBar);
    }

    public function test_from_array_with_empty_array(): void
    {
        $defaults = AlertDefaults::fromArray([]);

        $this->assertSame(Position::CENTER, $defaults->position);
        $this->assertSame(Theme::Auto, $defaults->theme);
        $this->assertSame('#3085d6', $defaults->confirmButtonColor);
        $this->assertSame('#aaa', $defaults->cancelButtonColor);
        $this->assertSame('#dd6b55', $defaults->denyButtonColor);
        $this->assertSame('OK', $defaults->confirmButtonText);
        $this->assertSame('Cancel', $defaults->cancelButtonText);
        $this->assertSame('No', $defaults->denyButtonText);
        $this->assertTrue($defaults->showConfirmButton);
        $this->assertFalse($defaults->showCancelButton);
        $this->assertFalse($defaults->showDenyButton);
        $this->assertFalse($defaults->reverseButtons);
        $this->assertTrue($defaults->backdrop);
        $this->assertSame([], $defaults->customClass);
        $this->assertTrue($defaults->animation);
        $this->assertTrue($defaults->allowOutsideClick);
        $this->assertTrue($defaults->allowEscapeKey);
        $this->assertTrue($defaults->focusConfirm);
        $this->assertFalse($defaults->draggable);
        $this->assertFalse($defaults->topLayer);
        $this->assertNull($defaults->timer);
        $this->assertFalse($defaults->timerProgressBar);
    }

    public function test_from_array_with_custom_values(): void
    {
        $config = [
            'position'           => 'top-end',
            'theme'              => 'dark',
            'confirmButtonColor' => '#ff0000',
            'cancelButtonColor'  => '#00ff00',
            'denyButtonColor'    => '#0000ff',
            'confirmButtonText'  => 'Confirmer',
            'cancelButtonText'   => 'Annuler',
            'denyButtonText'     => 'Non',
            'showConfirmButton'  => true,
            'showCancelButton'   => true,
            'showDenyButton'     => true,
            'reverseButtons'     => true,
            'backdrop'           => false,
            'customClass'        => ['popup' => 'my-popup'],
            'animation'          => false,
            'allowOutsideClick'  => false,
            'allowEscapeKey'     => false,
            'focusConfirm'       => false,
            'draggable'          => true,
            'topLayer'           => true,
            'timer'              => 3000,
            'timerProgressBar'   => true,
        ];

        $defaults = AlertDefaults::fromArray($config);

        $this->assertSame(Position::TOP_END, $defaults->position);
        $this->assertSame(Theme::Dark, $defaults->theme);
        $this->assertSame('#ff0000', $defaults->confirmButtonColor);
        $this->assertSame('#00ff00', $defaults->cancelButtonColor);
        $this->assertSame('#0000ff', $defaults->denyButtonColor);
        $this->assertSame('Confirmer', $defaults->confirmButtonText);
        $this->assertSame('Annuler', $defaults->cancelButtonText);
        $this->assertSame('Non', $defaults->denyButtonText);
        $this->assertTrue($defaults->showConfirmButton);
        $this->assertTrue($defaults->showCancelButton);
        $this->assertTrue($defaults->showDenyButton);
        $this->assertTrue($defaults->reverseButtons);
        $this->assertFalse($defaults->backdrop);
        $this->assertSame(['popup' => 'my-popup'], $defaults->customClass);
        $this->assertFalse($defaults->animation);
        $this->assertFalse($defaults->allowOutsideClick);
        $this->assertFalse($defaults->allowEscapeKey);
        $this->assertFalse($defaults->focusConfirm);
        $this->assertTrue($defaults->draggable);
        $this->assertTrue($defaults->topLayer);
        $this->assertSame(3000, $defaults->timer);
        $this->assertTrue($defaults->timerProgressBar);
    }

    public function test_from_array_config_theme_overrides_default_theme(): void
    {
        $config = [
            'theme' => 'light',
        ];

        $defaults = AlertDefaults::fromArray($config);

        $this->assertSame(Theme::Light, $defaults->theme);
    }

    public function test_from_array_with_partial_config(): void
    {
        $config = [
            'position'           => 'bottom-end',
            'confirmButtonColor' => '#00ff00',
        ];

        $defaults = AlertDefaults::fromArray($config);

        $this->assertSame(Position::BOTTOM_END, $defaults->position);
        $this->assertSame('#00ff00', $defaults->confirmButtonColor);
        // Other values should be defaults
        $this->assertSame('OK', $defaults->confirmButtonText);
        $this->assertTrue($defaults->showConfirmButton);
    }
}
