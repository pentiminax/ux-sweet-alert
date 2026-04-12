<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests;

use Pentiminax\UX\SweetAlert\AlertManager;
use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\Context\SweetAlertContext;
use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\Event\AlertQueuedEvent;
use Pentiminax\UX\SweetAlert\Event\BeforeAlertQueuedEvent;
use Pentiminax\UX\SweetAlert\FlashMessageConverter;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\AlertDefaults;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @internal
 */
#[CoversClass(AlertManager::class)]
final class AlertManagerTest extends KernelTestCase
{
    private AlertManager $alertManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->alertManager = $this->createAlertManager();
    }

    #[Test]
    #[DataProvider('alertMethodProvider')]
    public function it_creates_alerts_via_factory_methods(
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
            'id'                     => $alert->getId(),
            'title'                  => 'title',
            'text'                   => 'text',
            'icon'                   => $expectedIcon,
            'confirmButtonText'      => 'OK',
            'showConfirmButton'      => true,
            'showCancelButton'       => false,
            'showDenyButton'         => false,
            'reverseButtons'         => false,
            'animation'              => true,
            'theme'                  => 'auto',
            'allowEscapeKey'         => true,
            'confirmButtonColor'     => '#3085d6',
            'cancelButtonColor'      => '#aaa',
            'denyButtonColor'        => '#dd6b55',
            'position'               => 'bottom',
            'customClass'            => [],
            'cancelButtonText'       => 'Cancel',
            'html'                   => null,
            'footer'                 => null,
            'imageUrl'               => null,
            'imageHeight'            => null,
            'imageAlt'               => null,
            'draggable'              => false,
            'focusConfirm'           => true,
            'topLayer'               => false,
            'input'                  => null,
            'inputPlaceholder'       => null,
            'inputValue'             => null,
            'inputLabel'             => null,
            'inputAttributes'        => [],
            'inputOptions'           => null,
            'returnInputValueOnDeny' => null,
            'validationMessage'      => null,
            'backdrop'               => true,
            'allowOutsideClick'      => true,
        ];

        $this->assertSame($expectedArray, $alert->jsonSerialize());
    }

    #[Test]
    public function it_uses_configured_default_theme(): void
    {
        $alertManager = $this->createAlertManager(new AlertDefaults(theme: Theme::Dark));

        $alert = $alertManager->success(
            title: 'title',
            text: 'text',
        );

        $this->assertSame(Theme::Dark->value, $alert->jsonSerialize()['theme']);
    }

    #[Test]
    public function it_creates_toast_with_timer_and_progress_bar(): void
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

    #[Test]
    public function it_creates_toast_when_toast_flag_is_passed_to_success(): void
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
        $this->assertSame(Position::BOTTOM_END->value, $data['position']);
    }

    #[Test]
    public function it_preserves_custom_position_for_toasts(): void
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

    #[Test]
    public function it_applies_configured_default_theme_to_toasts(): void
    {
        $alertManager = $this->createAlertManager(new AlertDefaults(theme: Theme::Dark));

        $alert = $alertManager->toast(
            title: 'Toast',
            text: 'text'
        );

        $this->assertSame(Theme::Dark->value, $alert->jsonSerialize()['theme']);
    }

    #[Test]
    public function it_stores_alerts_in_session_attribute_not_flash_bag(): void
    {
        $session = new Session(new MockArraySessionStorage());
        $request = new Request();
        $request->setSession($session);
        $requestStack = new RequestStack();
        $requestStack->push($request);

        $alertDefaults = new AlertDefaults();
        $alertManager  = new AlertManager(
            requestStack: $requestStack,
            context: $this->createMock(SweetAlertContextInterface::class),
            flashMessageConverter: new FlashMessageConverter($alertDefaults),
            alertDefaults: $alertDefaults,
            eventDispatcher: $this->createStub(EventDispatcherInterface::class),
        );

        $alertManager->success(title: 'Test alert');

        $this->assertEmpty(
            $session->getFlashBag()->peekAll(),
            'Alert objects must not be stored in the FlashBag'
        );
    }

    #[Test]
    public function it_configures_input_alert_via_input_type(): void
    {
        $textInput = new \Pentiminax\UX\SweetAlert\InputType\Text(
            label: 'Enter your name',
            value: 'John Doe',
            placeholder: 'Name',
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
        $this->assertArrayNotHasKey('inputValidator', $data);
    }

    #[Test]
    public function it_allows_per_alert_theme_to_override_default(): void
    {
        $alert = $this->alertManager->success(
            title: 'title',
            theme: Theme::Dark,
        );

        $this->assertSame(Theme::Dark->value, $alert->jsonSerialize()['theme']);
    }

    #[Test]
    public function it_stores_callback_url_on_input_alert(): void
    {
        $alert = $this->alertManager->input(
            inputType: new \Pentiminax\UX\SweetAlert\InputType\Text(label: 'Name'),
            title: 'Enter name',
            callback: '/profile/update',
        );

        $data = $alert->jsonSerialize();

        $this->assertSame('/profile/update', $data['callbackUrl']);
    }

    #[Test]
    public function it_does_not_include_callback_url_when_not_provided(): void
    {
        $alert = $this->alertManager->input(
            inputType: new \Pentiminax\UX\SweetAlert\InputType\Text(label: 'Name'),
            title: 'Enter name',
        );

        $data = $alert->jsonSerialize();

        $this->assertArrayNotHasKey('callbackUrl', $data);
    }

    #[Test]
    public function it_dispatches_queue_events_in_order_and_persists_the_mutated_alert(): void
    {
        $context = new SweetAlertContext();
        $events  = [];

        $dispatcher = new class($events) implements EventDispatcherInterface {
            private array $events;

            public function __construct(array &$events)
            {
                $this->events = &$events;
            }

            public function dispatch(object $event, ?string $eventName = null): object
            {
                $this->events[] = $event::class;

                if ($event instanceof BeforeAlertQueuedEvent) {
                    $event->getAlert()
                        ->theme(Theme::Dark)
                        ->withCancelButton();
                }

                return $event;
            }
        };

        $alertManager = $this->createAlertManager(
            context: $context,
            dispatcher: $dispatcher,
        );

        $alert        = $alertManager->success(title: 'Queued alert');
        $storedAlerts = $alertManager->getSession()->get(AlertManagerInterface::ALERT_STORAGE_KEY, []);

        $this->assertSame([
            BeforeAlertQueuedEvent::class,
            AlertQueuedEvent::class,
        ], $events);
        $this->assertCount(1, $storedAlerts);
        $this->assertSame(Theme::Dark->value, $storedAlerts[0]->jsonSerialize()['theme']);
        $this->assertTrue($storedAlerts[0]->jsonSerialize()['showCancelButton']);
        $this->assertSame($alert, $storedAlerts[0]);
        $this->assertSame([$alert], $context->getAlerts());
    }

    #[Test]
    public function it_dispatches_events_for_input_alerts(): void
    {
        $events = [];

        $dispatcher = new class($events) implements EventDispatcherInterface {
            private array $events;

            public function __construct(array &$events)
            {
                $this->events = &$events;
            }

            public function dispatch(object $event, ?string $eventName = null): object
            {
                $this->events[] = $event::class;

                return $event;
            }
        };

        $alertManager = $this->createAlertManager(dispatcher: $dispatcher);

        $alertManager->input(
            inputType: new \Pentiminax\UX\SweetAlert\InputType\Text(label: 'Name'),
            title: 'Enter name',
        );

        $this->assertSame([
            BeforeAlertQueuedEvent::class,
            AlertQueuedEvent::class,
        ], $events);
    }

    #[Test]
    public function it_is_registered_in_the_test_container(): void
    {
        self::bootKernel();

        $service = self::getContainer()->get(AlertManagerInterface::class);

        $this->assertInstanceOf(AlertManager::class, $service);
    }

    public static function alertMethodProvider(): \Generator
    {
        yield ['success', 'success'];
        yield ['error', 'error'];
        yield ['warning', 'warning'];
        yield ['info', 'info'];
        yield ['question', 'question'];
    }

    private function createAlertManager(
        ?AlertDefaults $defaults = null,
        ?SweetAlertContextInterface $context = null,
        ?EventDispatcherInterface $dispatcher = null,
    ): AlertManager {
        $session = new Session(new MockArraySessionStorage());

        $request = new Request();
        $request->setSession($session);

        $requestStack = new RequestStack();
        $requestStack->push($request);

        $alertDefaults = $defaults ?? new AlertDefaults();

        return new AlertManager(
            requestStack: $requestStack,
            context: $context ?? $this->createMock(SweetAlertContextInterface::class),
            flashMessageConverter: new FlashMessageConverter($alertDefaults),
            alertDefaults: $alertDefaults,
            eventDispatcher: $dispatcher ?? $this->createStub(EventDispatcherInterface::class),
        );
    }
}
