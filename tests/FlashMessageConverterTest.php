<?php

declare(strict_types=1);

use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\FlashMessageConverter;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\AlertDefaults;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class FlashMessageConverterTest extends TestCase
{
    private FlashMessageConverter $converter;

    protected function setUp(): void
    {
        $this->converter = new FlashMessageConverter(new AlertDefaults());
    }

    #[DataProvider('provideFlashCases')]
    public function testConvert(string $key, array $messages, Icon $expectedIcon): void
    {
        $alerts = $this->converter->convert($key, $messages);

        self::assertIsArray($alerts);
        self::assertCount(\count($messages), $alerts);
        self::assertContainsOnlyInstancesOf(Alert::class, $alerts);

        foreach ($alerts as $i => $alert) {
            self::assertSame($messages[$i], $alert->getTitle());
            self::assertSame($expectedIcon, $alert->getIcon());
            // Position comes from AlertDefaults which defaults to CENTER
            self::assertSame(Position::CENTER, $alert->getPosition());
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
