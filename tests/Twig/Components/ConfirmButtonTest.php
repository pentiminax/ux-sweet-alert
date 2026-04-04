<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Twig\Components;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Tests\Fixtures\BrowserEventsTrait;
use Pentiminax\UX\SweetAlert\Twig\Components\ConfirmButton;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\UX\LiveComponent\LiveResponder;

/**
 * @internal
 */
#[CoversClass(ConfirmButton::class)]
final class ConfirmButtonTest extends TestCase
{
    use BrowserEventsTrait;

    #[Test]
    public function it_falls_back_to_empty_custom_class_when_json_is_invalid(): void
    {
        $component = new ConfirmButton();
        $component->setLiveResponder(new LiveResponder());
        $component->setTranslator(null);
        $component->title       = 'Archive';
        $component->text        = 'Archive this item?';
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

        $this->assertCount(1, $events);
        $this->assertSame('ux-sweet-alert:alert:added', $events[0]['event']);
    }
}
