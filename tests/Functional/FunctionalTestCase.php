<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Functional;

use Pentiminax\UX\SweetAlert\Tests\Functional\App\FunctionalKernel;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

abstract class FunctionalTestCase extends WebTestCase
{
    /**
     * @param array{sweet_alert?: array<string, mixed>} $options
     */
    protected static function createKernel(array $options = []): KernelInterface
    {
        return new FunctionalKernel($options['sweet_alert'] ?? []);
    }

    /**
     * @param array<string, mixed> $sweetAlertConfig
     */
    protected static function createAppClient(array $sweetAlertConfig = []): KernelBrowser
    {
        $client = static::createClient(['sweet_alert' => $sweetAlertConfig]);
        // Keep one container across requests so the session and recorded events survive redirects.
        $client->disableReboot();

        return $client;
    }

    /**
     * Alerts handed to the Stimulus controller by `ux_sweet_alert_scripts()` on the last HTML page.
     *
     * @return array<int, array<string, mixed>>
     */
    protected static function alertsOnPage(KernelBrowser $client): array
    {
        $node = $client->getCrawler()->filterXPath('//*[@data-controller]')->getNode(0);
        self::assertInstanceOf(\DOMElement::class, $node, 'The page does not render ux_sweet_alert_scripts().');

        foreach ($node->attributes as $attribute) {
            if (str_ends_with($attribute->name, '-view-value')) {
                return json_decode($attribute->value, true, 512, \JSON_THROW_ON_ERROR);
            }
        }

        self::fail('The Stimulus controller has no view value.');
    }

    /**
     * @return array<string, mixed>
     */
    protected static function jsonResponse(KernelBrowser $client): array
    {
        return json_decode((string) $client->getResponse()->getContent(), true, 512, \JSON_THROW_ON_ERROR);
    }
}
