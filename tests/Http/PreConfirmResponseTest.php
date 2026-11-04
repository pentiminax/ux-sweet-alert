<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Http;

use Pentiminax\UX\SweetAlert\Http\PreConfirmResponse;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validation;

/**
 * @internal
 */
#[CoversClass(PreConfirmResponse::class)]
final class PreConfirmResponseTest extends TestCase
{
    #[Test]
    public function it_returns_422_with_the_first_violation_message(): void
    {
        $violations = Validation::createValidator()->validate('', [
            new NotBlank(message: 'Required.'),
            new Length(min: 3, minMessage: 'Too short.'),
        ]);

        $response = PreConfirmResponse::fromViolations($violations, 'ignored');

        self::assertSame(422, $response->getStatusCode());
        self::assertSame('application/json', $response->headers->get('Content-Type'));
        self::assertSame('{"message":"Required."}', $response->getContent());
    }

    #[Test]
    public function it_returns_the_value_as_json_when_valid(): void
    {
        $response = PreConfirmResponse::fromViolations(new ConstraintViolationList(), ['username' => 'bob']);

        self::assertSame(200, $response->getStatusCode());
        self::assertSame('{"username":"bob"}', $response->getContent());
    }

    #[Test]
    public function it_returns_an_empty_204_without_value_so_the_input_is_kept(): void
    {
        $response = PreConfirmResponse::fromViolations(new ConstraintViolationList());

        self::assertSame(204, $response->getStatusCode());
        self::assertFalse($response->headers->has('Content-Type'));
        self::assertSame('', $response->getContent());
    }
}
