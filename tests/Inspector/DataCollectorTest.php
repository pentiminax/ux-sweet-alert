<?php

namespace Inspector;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContext;
use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Inspector\DataCollector;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\Toast;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DataCollectorTest extends TestCase
{
    private DataCollector $dataCollector;

    private SweetAlertContextInterface|MockObject $context;

    protected function setUp(): void
    {
        parent::setUp();

        $this->context = $this->createMock(SweetAlertContext::class);

        $this->dataCollector = new DataCollector(
            context: $this->context
        );
    }

    public function testGetName(): void
    {
        $this->assertSame('ux_sweetalert', $this->dataCollector->getName());
    }

    public function testGetData(): void
    {
        $alerts = [
            Alert::new('id', 'title', 'text')
        ];

        $this->context
            ->expects($this->once())
            ->method('getAlerts')
            ->willReturn($alerts);

        $toasts = [
            Toast::new('id', 'title', 'text')
        ];

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