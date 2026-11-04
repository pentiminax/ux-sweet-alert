<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Functional;

use Pentiminax\UX\SweetAlert\Delivery\AlertDelivery;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

/**
 * @internal
 */
final class BundleConfigurationTest extends FunctionalTestCase
{
    #[Test]
    public function it_builds_alerts_with_the_library_defaults_when_nothing_is_configured(): void
    {
        $client = self::createAppClient();

        $client->request('GET', '/json');

        $alert = self::jsonResponse($client)[AlertDelivery::JSON_KEY][0];
        self::assertSame('OK', $alert['confirmButtonText']);
        self::assertSame('auto', $alert['theme']);
        self::assertSame([], $alert['customClass']);
        self::assertTrue($alert['allowOutsideClick']);
        self::assertFalse($alert['draggable']);
    }

    #[Test]
    public function it_applies_configured_defaults_to_every_alert(): void
    {
        $client = self::createAppClient(['defaults' => [
            'confirmButtonText'  => 'Valider',
            'confirmButtonColor' => '#000000',
            'showCancelButton'   => true,
            'theme'              => 'dark',
            'allowOutsideClick'  => false,
            'draggable'          => true,
        ]]);

        $client->request('GET', '/json');

        $alert = self::jsonResponse($client)[AlertDelivery::JSON_KEY][0];
        self::assertSame('Valider', $alert['confirmButtonText']);
        self::assertSame('#000000', $alert['confirmButtonColor']);
        self::assertTrue($alert['showCancelButton']);
        self::assertSame('dark', $alert['theme']);
        self::assertFalse($alert['allowOutsideClick']);
        self::assertTrue($alert['draggable']);
    }

    #[Test]
    public function it_rejects_an_unknown_default_position(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('sweet_alert.defaults.position');

        self::bootKernel(['sweet_alert' => ['defaults' => ['position' => 'middle']]]);
    }

    #[Test]
    public function it_rejects_an_unknown_default_theme(): void
    {
        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessage('sweet_alert.defaults.theme');

        self::bootKernel(['sweet_alert' => ['defaults' => ['theme' => 'neon']]]);
    }
}
