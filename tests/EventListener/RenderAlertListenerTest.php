<?php

namespace Pentiminax\UX\SweetAlert\Tests\EventListener;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\EventListener\RenderAlertListener;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Twig\Environment;

class RenderAlertListenerTest extends TestCase
{
    private RenderAlertListener $listener;

    private AlertManagerInterface|MockObject $alertManager;

    private Environment|MockObject $twig;

    protected function setUp(): void
    {
        parent::setUp();

        $this->alertManager = $this->createMock(AlertManagerInterface::class);
        $this->twig = $this->createMock(Environment::class);

        $this->listener = new RenderAlertListener(
            alertManager: $this->alertManager,
            twig: $this->twig
        );
    }

    public function testOnKernelResponse(): void
    {
        $this->alertManager
            ->expects($this->once())
            ->method('getAlerts')
            ->willReturn([$this->givenAlert()]);

        $turboStramHtml = '<turbo-stream action="alert" target="alerts"><template>Alert content</template></turbo-stream>';

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                '@SweetAlert/turbo/alert.html.twig',
                $this->callback(fn(array $context) => $context['alert'] instanceof Alert)
            )
            ->willReturn($turboStramHtml);

        $response = new Response();

        $event = new ResponseEvent(
            kernel: $this->createMock(HttpKernelInterface::class),
            request: Request::create('/'),
            requestType: HttpKernelInterface::MAIN_REQUEST,
            response:$response
        );

        $this->listener->onKernelResponse($event);

        $this->assertSame($turboStramHtml, $event->getResponse()->getContent());
        $this->assertSame('text/vnd.turbo-stream.html', $event->getResponse()->headers->get('Content-Type'));
    }

    private function givenAlert(): Alert
    {
        return Alert::new(
            id: 'id',
            title: 'title',
            text: 'Text',
        );
    }
}