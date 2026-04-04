<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\DataCollector;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContext;
use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\DataCollector\SweetAlertDataCollector;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @internal
 */
#[CoversClass(SweetAlertDataCollector::class)]
final class DataCollectorTest extends TestCase
{
    private SweetAlertDataCollector $dataCollector;

    private SweetAlertContextInterface|MockObject $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = $this->createMock(SweetAlertContext::class);

        $this->dataCollector = new SweetAlertDataCollector(
            context: $this->context
        );
    }

    #[Test]
    public function it_returns_the_correct_collector_name(): void
    {
        $this->assertSame('ux_sweetalert', $this->dataCollector->getName());
    }

    #[Test]
    public function it_collects_alerts_and_toasts_from_context(): void
    {
        $alerts = [
            Alert::new('title', 'id', 'text'),
        ];

        $this->context
            ->expects($this->once())
            ->method('getStandardAlerts')
            ->willReturn($alerts);

        $toastAlert = Alert::new('toast-title', 'toast-id', 'text');
        $toastAlert->asToast();
        $toasts = [$toastAlert];

        $this->context
            ->expects($this->once())
            ->method('getToasts')
            ->willReturn($toasts);

        $this->dataCollector->collect(
            Request::create('/'),
            new Response()
        );

        $data = $this->dataCollector->getData();
        $this->assertCount(1, $data['alerts']);
        $this->assertCount(1, $data['toasts']);
    }
}
