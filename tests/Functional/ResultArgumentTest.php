<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;

/**
 * The Stimulus controller posts the SweetAlert result to `callbackUrl`; controllers receive it as a Result argument.
 *
 * @internal
 */
final class ResultArgumentTest extends FunctionalTestCase
{
    #[Test]
    public function it_injects_the_posted_result_into_the_controller(): void
    {
        $client = self::createAppClient();

        $client->jsonRequest('POST', '/result', ['isConfirmed' => true, 'value' => 'Tanguy']);

        self::assertResponseIsSuccessful();
        self::assertSame(
            ['isConfirmed' => true, 'isDenied' => false, 'isDismissed' => false, 'value' => 'Tanguy'],
            self::jsonResponse($client),
        );
    }

    #[Test]
    public function it_injects_a_denied_result_with_its_input_value(): void
    {
        $client = self::createAppClient();

        $client->jsonRequest('POST', '/result', ['isDenied' => true, 'value' => ['plan' => 'pro']]);

        self::assertSame(
            ['isConfirmed' => false, 'isDenied' => true, 'isDismissed' => false, 'value' => ['plan' => 'pro']],
            self::jsonResponse($client),
        );
    }
}
