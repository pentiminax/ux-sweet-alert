<?php

namespace Pentiminax\UX\SweetAlert\Tests\Twig\Extension;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\Twig\Extension\AlertExtension;
use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

class AlertExtensionTest extends TestCase
{
    public function testScriptsAddsTurboTemporaryAttribute(): void
    {
        $twig = new Environment(new ArrayLoader());

        $alertManager = $this->createMock(AlertManagerInterface::class);
        $alertManager
            ->expects($this->once())
            ->method('getAlerts')
            ->willReturn([]);

        $extension = new AlertExtension($twig, $alertManager);

        $markup = $extension->scripts();

        $this->assertStringContainsString('data-turbo-temporary', (string) $markup);
    }
}
