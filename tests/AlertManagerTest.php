<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests;

use Pentiminax\UX\SweetAlert\AlertManager;
use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\FlashMessageConverter;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\AlertDefaults;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

/**
 * @internal
 */
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

        $alertDefaults = new AlertDefaults();

        $this->alertManager = new AlertManager(
            requestStack: $requestStack,
            context: $this->createMock(SweetAlertContextInterface::class),
            flashMessageConverter: new FlashMessageConverter($alertDefaults),
            alertDefaults: $alertDefaults,
        );
    }

    #[DataProvider('alertMethodProvider')]
    public function test_alert_factory_methods(
        string $method,
        string $expectedIcon,
    ): void {
        /** @var Alert $alert */
        $alert = $this->alertManager->$method(
            title: 'title',
            text: 'text',
            position: Position::BOTTOM
        );

        $expectedArray = [
            'id'                 => $alert->getId(),
            'title'              => 'title',
            'text'               => 'text',
            'icon'               => $expectedIcon,
            'confirmButtonText'  => 'OK',
            'showConfirmButton'  => true,
            'showCancelButton'   => false,
            'showDenyButton'     => false,
            'reverseButtons'     => false,
            'animation'          => true,
            'theme'              => 'auto',
            'allowEscapeKey'     => true,
            'confirmButtonColor' => '#3085d6',
            'cancelButtonColor'  => '#aaa',
            'denyButtonColor'    => '#dd6b55',
            'position'           => 'bottom',
            'customClass'        => [],
            'cancelButtonText'   => 'Cancel',
            'html'               => null,
            'backdrop'           => true,
            'allowOutsideClick'  => true,
            'footer'             => null,
            'imageUrl'           => null,
            'imageHeight'        => null,
            'imageAlt'           => null,
            'draggable'          => false,
            'focusConfirm'       => true,
            'topLayer'           => false,
            'input'              => null,
            'inputPlaceholder'   => null,
            'inputValue'         => null,
            'inputLabel'         => null,
            'inputAttributes'    => [],
            'inputValidator'     => null,
        ];

        $this->assertEquals($expectedArray, $alert->jsonSerialize());
    }

    public function test_uses_configured_default_theme(): void
    {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $alertDefaults = new AlertDefaults(theme: Theme::Dark);

        $alertManager = new AlertManager(
            requestStack: $requestStack,
            context: $this->createMock(SweetAlertContextInterface::class),
            flashMessageConverter: new FlashMessageConverter($alertDefaults),
            alertDefaults: $alertDefaults,
        );

        $alert = $alertManager->success(
            title: 'title',
            text: 'text',
        );

        $this->assertSame(Theme::Dark->value, $alert->jsonSerialize()['theme']);
    }

    public function test_toast_method(): void
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

    public function test_alert_with_toast_flag(): void
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

    public function test_toast_with_custom_position(): void
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

    public function test_toast_uses_configured_theme(): void
    {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $alertDefaults = new AlertDefaults(theme: Theme::Dark);

        $alertManager = new AlertManager(
            requestStack: $requestStack,
            context: $this->createMock(SweetAlertContextInterface::class),
            flashMessageConverter: new FlashMessageConverter($alertDefaults),
            alertDefaults: $alertDefaults,
        );

        $alert = $alertManager->toast(
            title: 'Toast',
            text: 'text'
        );

        $this->assertSame(Theme::Dark->value, $alert->jsonSerialize()['theme']);
    }

    public function test_alerts_are_not_stored_in_flash_bag(): void
    {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $alertDefaults = new AlertDefaults();

        $alertManager = new AlertManager(
            requestStack: $requestStack,
            context: $this->createMock(SweetAlertContextInterface::class),
            flashMessageConverter: new FlashMessageConverter($alertDefaults),
            alertDefaults: $alertDefaults,
        );

        $alertManager->success(title: 'Test alert');

        $this->assertEmpty(
            $session->getFlashBag()->peekAll(),
            'Alert objects must not be stored in the FlashBag'
        );
    }

    public function test_input_method(): void
    {
        $textInput = new \Pentiminax\UX\SweetAlert\InputType\Text(
            label: 'Enter your name',
            value: 'John Doe',
            placeholder: 'Name',
            validator: 'required'
        );

        $alert = $this->alertManager->input(
            inputType: $textInput,
            title: 'Please enter your name',
            text: 'We need it for verification'
        );

        $data = $alert->jsonSerialize();

        $this->assertSame('Please enter your name', $data['title']);
        $this->assertSame('We need it for verification', $data['text']);
        $this->assertSame('text', $data['input']);
        $this->assertSame('Enter your name', $data['inputLabel']);
        $this->assertSame('John Doe', $data['inputValue']);
        $this->assertSame('Name', $data['inputPlaceholder']);
        $this->assertSame('required', $data['inputValidator']);
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
