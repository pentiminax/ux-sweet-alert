<?php

declare(strict_types=1);

namespace App\Tests;

use App\Examples;
use App\Page;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Catches a demo broken by a bundle change: every page renders and every example runs.
 */
final class DemoTest extends WebTestCase
{
    /**
     * @return iterable<string, array{string}>
     */
    public static function urls(): iterable
    {
        yield 'home' => ['/'];
        foreach (Page::cases() as $page) {
            yield $page->value => ['/'.$page->value];
        }
    }

    #[DataProvider('urls')]
    public function testPageRenders(string $url): void
    {
        $client = self::createClient();
        $client->request('GET', $url);

        self::assertResponseIsSuccessful();
    }

    /**
     * @return iterable<string, array{string, string}>
     */
    public static function examples(): iterable
    {
        foreach ([Examples::HOME, ...array_map(static fn (Page $page): string => $page->value, Page::cases())] as $page) {
            foreach (array_keys(Examples::for($page)) as $example) {
                yield $page.'/'.$example => [$page, $example];
            }
        }
    }

    #[DataProvider('examples')]
    public function testExampleQueuesAnAlert(string $page, string $example): void
    {
        $client = self::createClient();
        $client->request('POST', \sprintf('/fire/%s/%s', $page, $example), ['position' => 'top', 'theme' => 'dark']);

        self::assertResponseStatusCodeSame(303);
        $client->followRedirect();
        self::assertSelectorExists('[data-controller]');
    }
}
