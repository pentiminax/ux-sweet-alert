<?php

namespace Pentiminax\UX\SweetAlert\Tests;

use Pentiminax\UX\SweetAlert\AlertManager;
use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\FlashMessageConverter;
use Pentiminax\UX\SweetAlert\Model\Alert;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class AlertManagerTest extends KernelTestCase
{
    private AlertManager $alertManager;

    protected function setUp(): void
    {
        parent::setUp();

        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $this->alertManager = new AlertManager(
            requestStack: $requestStack,
            context: $this->createMock(SweetAlertContextInterface::class),
            flashMessageConverter: new FlashMessageConverter(),
        );
    }

    #[DataProvider('alertMethodProvider')]
    public function testAlertFactoryMethods(
        string $method,
        string $expectedIcon
    ): void {
        /** @var Alert $alert */
        $alert = $this->alertManager->$method(
            title: 'title',
            text: 'text',
            position: Position::BOTTOM
        );

        $expectedArray = [
            'id' => $alert->getId(),
            'title' => 'title',
            'text' => 'text',
            'icon' => $expectedIcon,
            'confirmButtonText' => 'OK',
            'showConfirmButton' => true,
            'showCancelButton' => false,
            'showDenyButton' => false,
            'animation' => true,
            'theme' => 'auto',
            'allowEscapeKey' => true,
            'confirmButtonColor' => '#3085d6',
            'position' => 'bottom',
            'customClass' => [],
            'cancelButtonText' => 'Cancel',
            'html' => null,
            'backdrop' => true,
            'allowOutsideClick' => true,
        ];

        $this->assertEquals($expectedArray, $alert->jsonSerialize());
    }

    public function testUsesConfiguredDefaultTheme(): void
    {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $alertManager = new AlertManager(
            requestStack: $requestStack,
            context: $this->createMock(SweetAlertContextInterface::class),
            flashMessageConverter: new FlashMessageConverter(Theme::Dark->value),
            defaultTheme: Theme::Dark->value
        );

        $alert = $alertManager->success(
            title: 'title',
            text: 'text',
            position: Position::CENTER
        );

        $this->assertSame(Theme::Dark->value, $alert->jsonSerialize()['theme']);
    }

    public function testToastMethod(): void
    {
        $alert = $this->alertManager->toast(
            title: 'Toast notification',
            text: 'This is a toast',
            timer: 3000,
            timerProgressBar: true
        );

        $data = $alert->jsonSerialize();

        $this->assertTrue($alert->isToast());
        $this->assertTrue($data['toast']);
        $this->assertSame(3000, $data['timer']);
        $this->assertTrue($data['timerProgressBar']);
        $this->assertFalse($data['showConfirmButton']);
        $this->assertSame(Position::BOTTOM_END->value, $data['position']);
        $this->assertArrayNotHasKey('backdrop', $data);
        $this->assertArrayNotHasKey('allowOutsideClick', $data);
    }

    public function testAlertWithToastFlag(): void
    {
        $alert = $this->alertManager->success(
            title: 'Success toast',
            text: 'This is a toast',
            toast: true,
            timer: 5000
        );

        $data = $alert->jsonSerialize();

        $this->assertTrue($alert->isToast());
        $this->assertTrue($data['toast']);
        $this->assertSame(5000, $data['timer']);
        $this->assertFalse($data['showConfirmButton']);
        // When toast=true and position=CENTER (default), it should change to BOTTOM_END
        $this->assertSame(Position::BOTTOM_END->value, $data['position']);
    }

    public function testToastWithCustomPosition(): void
    {
        $alert = $this->alertManager->success(
            title: 'Custom position toast',
            position: Position::TOP_END,
            toast: true
        );

        $data = $alert->jsonSerialize();

        $this->assertTrue($data['toast']);
        $this->assertSame(Position::TOP_END->value, $data['position']);
    }

    public function testToastUsesConfiguredTheme(): void
    {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $alertManager = new AlertManager(
            requestStack: $requestStack,
            context: $this->createMock(SweetAlertContextInterface::class),
            flashMessageConverter: new FlashMessageConverter(Theme::Dark->value),
            defaultTheme: Theme::Dark->value
        );

        $alert = $alertManager->toast(
            title: 'Toast',
            text: 'text'
        );

        $this->assertSame(Theme::Dark->value, $alert->jsonSerialize()['theme']);
    }

    public static function alertMethodProvider(): \Generator
    {
        yield ['success', 'success'];
        yield ['error', 'error'];
        yield ['warning', 'warning'];
        yield ['info', 'info'];
        yield ['question', 'question'];
    }
}
