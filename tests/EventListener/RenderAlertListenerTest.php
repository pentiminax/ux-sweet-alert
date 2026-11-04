<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\EventListener;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\Delivery\AlertDelivery;
use Pentiminax\UX\SweetAlert\EventListener\RenderAlertListener;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @internal
 */
#[CoversClass(RenderAlertListener::class)]
final class RenderAlertListenerTest extends TestCase
{
    #[Test]
    public function it_skips_sub_requests(): void
    {
        $alertManager = $this->createMock(AlertManagerInterface::class);
        $alertManager->expects($this->never())->method('getAlerts');

        $request = new Request();
        $request->headers->set('HX-Request', 'true');

        $delivery = new AlertDelivery($alertManager, new Environment(new ArrayLoader()), new RequestStack());
        $event    = new ResponseEvent($this->createStub(HttpKernelInterface::class), $request, HttpKernelInterface::SUB_REQUEST, new Response());

        (new RenderAlertListener($delivery))->onKernelResponse($event);
    }
}
