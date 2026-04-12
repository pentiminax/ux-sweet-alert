<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\ValueResolver;

use Pentiminax\UX\SweetAlert\Model\Result;
use Pentiminax\UX\SweetAlert\ValueResolver\ResultValueResolver;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

/**
 * @internal
 */
#[CoversClass(ResultValueResolver::class)]
final class ResultValueResolverTest extends TestCase
{
    private ResultValueResolver $resolver;

    protected function setUp(): void
    {
        $this->resolver = new ResultValueResolver();
    }

    #[Test]
    public function it_resolves_result_from_json_body(): void
    {
        $request = Request::create('/', 'POST', content: json_encode([
            'isConfirmed' => true,
            'isDenied'    => false,
            'isDismissed' => false,
            'value'       => 'Tanguy',
        ]));

        $argument = $this->createArgumentMetadata(Result::class);

        $results = [...$this->resolver->resolve($request, $argument)];

        $this->assertCount(1, $results);
        $this->assertInstanceOf(Result::class, $results[0]);
        $this->assertTrue($results[0]->isConfirmed);
        $this->assertFalse($results[0]->isDenied);
        $this->assertFalse($results[0]->isDismissed);
        $this->assertSame('Tanguy', $results[0]->value);
    }

    #[Test]
    public function it_resolves_dismissed_result(): void
    {
        $request = Request::create('/', 'POST', content: json_encode([
            'isConfirmed' => false,
            'isDenied'    => false,
            'isDismissed' => true,
            'value'       => null,
        ]));

        $argument = $this->createArgumentMetadata(Result::class);

        $results = [...$this->resolver->resolve($request, $argument)];

        $this->assertCount(1, $results);
        $this->assertFalse($results[0]->isConfirmed);
        $this->assertTrue($results[0]->isDismissed);
        $this->assertNull($results[0]->value);
    }

    #[Test]
    public function it_returns_empty_when_argument_type_is_not_result(): void
    {
        $request  = Request::create('/', 'POST', content: '{}');
        $argument = $this->createArgumentMetadata(\stdClass::class);

        $results = [...$this->resolver->resolve($request, $argument)];

        $this->assertEmpty($results);
    }

    #[Test]
    public function it_returns_empty_when_body_is_not_valid_json(): void
    {
        $request  = Request::create('/', 'POST', content: 'not-json');
        $argument = $this->createArgumentMetadata(Result::class);

        $results = [...$this->resolver->resolve($request, $argument)];

        $this->assertEmpty($results);
    }

    #[Test]
    public function it_returns_empty_when_body_is_empty(): void
    {
        $request  = Request::create('/', 'POST', content: '');
        $argument = $this->createArgumentMetadata(Result::class);

        $results = [...$this->resolver->resolve($request, $argument)];

        $this->assertEmpty($results);
    }

    private function createArgumentMetadata(string $type): ArgumentMetadata
    {
        $argument = $this->createMock(ArgumentMetadata::class);
        $argument->method('getType')->willReturn($type);

        return $argument;
    }
}
