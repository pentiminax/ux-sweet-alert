<?php

namespace Pentiminax\UX\SweetAlert\Tests;

use Pentiminax\UX\SweetAlert\AlertManager;
use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Enum\Position;
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
            context: $this->createMock(SweetAlertContextInterface::class)
        );
    }

    #[DataProvider('alertMethodProvider')]
    public function testToastFactoryMethods(
        string $method,
        string $expectedIcon
    ): void {
        $alert = $this->alertManager->$method(
            id: 'id',
            title: 'title',
            text: 'text',
            position: Position::BOTTOM
        );

        $expectedArray = [
            'id' => 'id',
            'title' => 'title',
            'text' => 'text',
            'icon' => $expectedIcon,
            'confirmButtonText' => 'OK',
            'showConfirmButton' => true,
            'showCancelButton' => false,
            'showDenyButton' => false,
            'animation' => true,
            'theme' => 'auto',
            'backdrop' => true,
            'allowOutsideClick' => true,
            'allowEscapeKey' => true,
            'confirmButtonColor' => '#3085d6',
            'position' => 'bottom',
            'customClass' => [],
            'cancelButtonText' => 'Cancel',
        ];

        $this->assertEquals($expectedArray, $alert->jsonSerialize());
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