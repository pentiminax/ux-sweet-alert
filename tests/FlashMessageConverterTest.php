<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests;

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\FlashMessageConverter;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\AlertDefaults;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
#[CoversClass(FlashMessageConverter::class)]
final class FlashMessageConverterTest extends TestCase
{
    private FlashMessageConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new FlashMessageConverter(new AlertDefaults());
    }

    #[Test]
    #[DataProvider('provideFlashCases')]
    public function it_converts_flash_messages_to_alerts(string $key, array $messages, Icon $expectedIcon): void
    {
        $alerts = $this->converter->convert($key, $messages);

        $this->assertIsArray($alerts);
        $this->assertCount(\count($messages), $alerts);
        $this->assertContainsOnlyInstancesOf(Alert::class, $alerts);

        foreach ($alerts as $i => $alert) {
            $this->assertSame($messages[$i], $alert->getTitle());
            $this->assertSame($expectedIcon, $alert->getIcon());
            // Position comes from AlertDefaults which defaults to CENTER
            $this->assertSame(Position::CENTER, $alert->getPosition());
        }
    }

    public static function provideFlashCases(): iterable
    {
        yield 'error -> ERROR' => [
            'error',
            ['Oops', 'Une erreur est survenue'],
            Icon::ERROR,
        ];

        yield 'warning -> WARNING' => [
            'warning',
            ['Attention !'],
            Icon::WARNING,
        ];

        yield 'info -> INFO' => [
            'info',
            ['Information utile'],
            Icon::INFO,
        ];

        yield 'notice -> INFO (alias)' => [
            'notice',
            ['Petit rappel'],
            Icon::INFO,
        ];

        yield 'question -> QUESTION' => [
            'question',
            ['Êtes-vous sûr ?'],
            Icon::QUESTION,
        ];

        yield 'default (success) -> SUCCESS' => [
            'success',
            ['Tout est bon'],
            Icon::SUCCESS,
        ];

        yield 'type inconnu -> SUCCESS par défaut' => [
            'whatever',
            ['Message inconnu'],
            Icon::SUCCESS,
        ];

        yield 'aucun message -> tableau vide' => [
            'info',
            [],
            Icon::INFO,
        ];
    }
}
