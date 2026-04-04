<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\EventListener;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\EventListener\RenderAlertListener;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Tests\Fixtures\DummyTurboBundle;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Twig\Environment;

/**
 * @internal
 */
#[CoversClass(RenderAlertListener::class)]
final class RenderAlertListenerTest extends TestCase
{
    private RenderAlertListener $listener;

    private AlertManagerInterface|MockObject $alertManager;

    private Environment|MockObject $twig;

    protected function setUp(): void
    {
        parent::setUp();

        if (!class_exists('Symfony\UX\Turbo\TurboBundle')) {
            class_alias(DummyTurboBundle::class, 'Symfony\UX\Turbo\TurboBundle');
        }

        $this->alertManager = $this->createMock(AlertManagerInterface::class);
        $this->twig         = $this->createMock(Environment::class);

        $this->listener = new RenderAlertListener(
            alertManager: $this->alertManager,
            twig: $this->twig
        );
    }

    #[Test]
    public function it_appends_turbo_stream_html_to_the_response(): void
    {
        $this->alertManager
            ->expects($this->once())
            ->method('getAlerts')
            ->willReturn([$this->givenAlert()]);

        $turboStreamHtml = '<turbo-stream action="alert"><template>Alert content</template></turbo-stream>';

        $this->twig
            ->expects($this->once())
            ->method('render')
            ->with(
                '@SweetAlert/turbo/alert.html.twig',
                $this->callback(fn (array $context) => $context['alert'] instanceof Alert)
            )
            ->willReturn($turboStreamHtml);

        $response = new Response(
            headers: ['Content-Type' => 'text/html'],
        );

        $event = new ResponseEvent(
            kernel: $this->createMock(HttpKernelInterface::class),
            request: Request::create('/'),
            requestType: HttpKernelInterface::MAIN_REQUEST,
            response: $response
        );

        $this->listener->onKernelResponse($event);

        $this->assertSame($turboStreamHtml, $event->getResponse()->getContent());
        $this->assertSame('text/html', $event->getResponse()->headers->get('Content-Type'));
    }

    private function givenAlert(): Alert
    {
        return Alert::new(
            title: 'title',
            text: 'Text',
        );
    }
}
