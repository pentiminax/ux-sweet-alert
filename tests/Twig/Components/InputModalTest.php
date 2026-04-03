<?php

namespace Pentiminax\UX\SweetAlert\Tests\Twig\Components;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Model\Result;
use Pentiminax\UX\SweetAlert\Twig\Components\InputModal;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\LiveResponder;

class InputModalTest extends TestCase
{
    public function testAlertAddedBuildsConfiguredInputAlertAndDispatchesBrowserEvent(): void
    {
        $component = new InputModal();
        $component->setLiveResponder(new LiveResponder());
        $component->setTranslator(null);
        $component->title = 'Profile update';
        $component->text = 'Pick your role';
        $component->icon = 'question';
        $component->callback = 'handleRoleSelection';
        $component->customClass = '{"popup":"role-modal"}';
        $component->confirmButtonText = 'Save';
        $component->cancelButtonText = 'Dismiss';
        $component->inputType = 'select';
        $component->inputLabel = 'Role';
        $component->inputPlaceholder = 'Ignored for select';
        $component->inputValue = 'admin';
        $component->inputOptions = '{"admin":"Admin","editor":"Editor"}';
        $component->inputAttributes = '{"data-test":"role-select"}';
        $component->validationMessage = 'Role is required';
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

        self::assertCount(1, $events);
        self::assertSame('ux-sweet-alert:alert:added', $events[0]['event']);
        self::assertSame('handleRoleSelection', $events[0]['payload']['callback']);
        self::assertSame('Profile update', $events[0]['payload']['alert']->jsonSerialize()['title']);
    }

    public function testAlertAddedIgnoresInvalidJsonConfiguration(): void
    {
        $component = new InputModal();
        $component->setLiveResponder(new LiveResponder());
        $component->setTranslator(null);
        $component->title = 'Broken payload';
        $component->customClass = '{invalid';
        $component->inputOptions = '{invalid';
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

    public function testCallbackActionCallsOnResultAndDispatchesBrowserEvent(): void
    {
        $component = new TestableInputModal();
        $component->setLiveResponder(new LiveResponder());
        $component->setTranslator(null);

        $result = [
            'isConfirmed' => true,
            'isDenied' => false,
            'isDismissed' => false,
            'value' => 'editor',
        ];
        $args = ['id' => '42'];

        $component->callbackAction($result, $args);

        self::assertTrue($component->receivedResult->isConfirmed);
        self::assertSame('editor', $component->receivedResult->value);
        self::assertSame(['id' => '42'], $component->receivedArgs);

        $events = $this->browserEvents($component);

        self::assertCount(1, $events);
        self::assertSame('ux-sweet-alert:callback', $events[0]['event']);
        self::assertSame($result, $events[0]['payload']['result']);
        self::assertSame($args, $events[0]['payload']['args']);
    }

    public function testIsDisabledReflectsPublicProperty(): void
    {
        $component = new InputModal();

        self::assertFalse($component->isDisabled());

        $component->disabled = true;

        self::assertTrue($component->isDisabled());
    }

    private function browserEvents(InputModal $component): array
    {
        $reflection = new \ReflectionClass(InputModal::class);
        $property = $reflection->getProperty('liveResponder');
        $property->setAccessible(true);

        return $property->getValue($component)->getBrowserEventsToDispatch();
    }
}

final class TestableInputModal extends InputModal
{
    public ?Result $receivedResult = null;

    public array $receivedArgs = [];

    protected function onResult(Result $result, array $args = []): void
    {
        $this->receivedResult = $result;
        $this->receivedArgs = $args;
    }
}
