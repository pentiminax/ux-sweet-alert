<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Twig\Components;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Tests\Fixtures\BrowserEventsTrait;
use Pentiminax\UX\SweetAlert\Tests\Fixtures\TestableInputModal;
use Pentiminax\UX\SweetAlert\Twig\Components\InputModal;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\LiveResponder;

/**
 * @internal
 */
#[CoversClass(InputModal::class)]
final class InputModalTest extends TestCase
{
    use BrowserEventsTrait;

    #[Test]
    public function it_builds_configured_input_alert_and_dispatches_browser_event(): void
    {
        $component = new InputModal();
        $component->setLiveResponder(new LiveResponder());
        $component->setTranslator(null);
        $component->title                  = 'Profile update';
        $component->text                   = 'Pick your role';
        $component->icon                   = 'question';
        $component->callback               = 'handleRoleSelection';
        $component->customClass            = '{"popup":"role-modal"}';
        $component->confirmButtonText      = 'Save';
        $component->cancelButtonText       = 'Dismiss';
        $component->inputType              = 'select';
        $component->inputLabel             = 'Role';
        $component->inputPlaceholder       = 'Ignored for select';
        $component->inputValue             = 'admin';
        $component->inputOptions           = '{"admin":"Admin","editor":"Editor"}';
        $component->inputAttributes        = '{"data-test":"role-select"}';
        $component->validationMessage      = 'Role is required';
        $component->returnInputValueOnDeny = true;

        $context = $this->createMock(SweetAlertContextInterface::class);
        $context
            ->expects($this->once())
            ->method('addAlert')
            ->with($this->callback(static function (object $alert): bool {
                $data = $alert->jsonSerialize();

                self::assertSame('Profile update', $data['title']);
                self::assertSame('Pick your role', $data['text']);
                self::assertSame('question', $data['icon']);
                self::assertSame('Save', $data['confirmButtonText']);
                self::assertSame('Dismiss', $data['cancelButtonText']);
                self::assertTrue($data['showCancelButton']);
                self::assertSame(['popup' => 'role-modal'], $data['customClass']);
                self::assertSame('select', $data['input']);
                self::assertSame('Role', $data['inputLabel']);
                self::assertSame('Ignored for select', $data['inputPlaceholder']);
                self::assertSame('admin', $data['inputValue']);
                self::assertSame(['admin' => 'Admin', 'editor' => 'Editor'], $data['inputOptions']);
                self::assertSame(['data-test' => 'role-select'], $data['inputAttributes']);
                self::assertSame('Role is required', $data['validationMessage']);
                self::assertTrue($data['returnInputValueOnDeny']);

                return true;
            }));

        $component->setContext($context);
        $component->alertAdded();

        $events = $this->browserEvents($component);

        $this->assertCount(1, $events);
        $this->assertSame('ux-sweet-alert:alert:added', $events[0]['event']);
        $this->assertSame('handleRoleSelection', $events[0]['payload']['callback']);
        $this->assertSame('Profile update', $events[0]['payload']['alert']->jsonSerialize()['title']);
    }

    #[Test]
    public function it_ignores_invalid_json_in_custom_class_input_options_and_attributes(): void
    {
        $component = new InputModal();
        $component->setLiveResponder(new LiveResponder());
        $component->setTranslator(null);
        $component->title           = 'Broken payload';
        $component->customClass     = '{invalid';
        $component->inputOptions    = '{invalid';
        $component->inputAttributes = '{invalid';

        $context = $this->createMock(SweetAlertContextInterface::class);
        $context
            ->expects($this->once())
            ->method('addAlert')
            ->with($this->callback(static function (object $alert): bool {
                $data = $alert->jsonSerialize();

                self::assertSame([], $data['customClass']);
                self::assertNull($data['inputOptions']);
                self::assertSame([], $data['inputAttributes']);

                return true;
            }));

        $component->setContext($context);
        $component->alertAdded();
    }

    #[Test]
    public function it_calls_on_result_hook_and_dispatches_callback_browser_event(): void
    {
        $component = new TestableInputModal();
        $component->setLiveResponder(new LiveResponder());
        $component->setTranslator(null);

        $result = [
            'isConfirmed' => true,
            'isDenied'    => false,
            'isDismissed' => false,
            'value'       => 'editor',
        ];
        $args = ['id' => '42'];

        $component->callbackAction($result, $args);

        $this->assertTrue($component->receivedResult->isConfirmed);
        $this->assertSame('editor', $component->receivedResult->value);
        $this->assertSame(['id' => '42'], $component->receivedArgs);

        $events = $this->browserEvents($component);

        $this->assertCount(1, $events);
        $this->assertSame('ux-sweet-alert:callback', $events[0]['event']);
        $this->assertSame($result, $events[0]['payload']['result']);
        $this->assertSame($args, $events[0]['payload']['args']);
    }

    #[Test]
    public function it_reflects_disabled_state_from_public_property(): void
    {
        $component = new InputModal();

        $this->assertFalse($component->isDisabled());

        $component->disabled = true;

        $this->assertTrue($component->isDisabled());
    }
}
