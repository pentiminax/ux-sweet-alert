<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Twig\Extension;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\Delivery\AlertDelivery;
use Pentiminax\UX\SweetAlert\Twig\Extension\AlertExtension;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 */
#[CoversClass(AlertExtension::class)]
final class AlertExtensionTest extends TestCase
{
    #[Test]
    public function it_adds_turbo_temporary_attribute_to_scripts_markup(): void
    {
        $twig = new Environment(new ArrayLoader());

        $session = new Session(new MockArraySessionStorage());
        $session->start();
        $request = new Request();
        $request->setSession($session);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $alertManager = $this->createMock(AlertManagerInterface::class);
        $alertManager
            ->expects($this->once())
            ->method('getAlerts')
            ->willReturn([]);

        $extension = new AlertExtension($twig, new AlertDelivery($alertManager, $twig, $requestStack));

        $markup = $extension->scripts();

        $this->assertStringContainsString('data-turbo-temporary', (string) $markup);
    }
}
