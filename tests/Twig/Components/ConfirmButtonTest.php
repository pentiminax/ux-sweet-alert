<?php

namespace Pentiminax\UX\SweetAlert\Tests\Twig\Components;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Twig\Components\ConfirmButton;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\LiveResponder;

class ConfirmButtonTest extends TestCase
{
    public function testAlertAddedFallsBackToEmptyCustomClassWhenJsonIsInvalid(): void
    {
        $component = new ConfirmButton();
        $component->setLiveResponder(new LiveResponder());
        $component->setTranslator(null);
        $component->title = 'Archive';
        $component->text = 'Archive this item?';
        $component->customClass = '{invalid';

        $context = $this->createMock(SweetAlertContextInterface::class);
        $context
            ->expects($this->once())
            ->method('addAlert')
            ->with($this->callback(static function (object $alert): bool {
                self::assertSame([], $alert->jsonSerialize()['customClass']);

                return true;
            }));

        $component->setContext($context);
        $component->alertAdded();

        $events = $this->browserEvents($component);

        self::assertCount(1, $events);
        self::assertSame('ux-sweet-alert:alert:added', $events[0]['event']);
    }

    private function browserEvents(ConfirmButton $component): array
    {
        $reflection = new \ReflectionClass(ConfirmButton::class);
        $property = $reflection->getProperty('liveResponder');
        $property->setAccessible(true);

        return $property->getValue($component)->getBrowserEventsToDispatch();
    }
}
