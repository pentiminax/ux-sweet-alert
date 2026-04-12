<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\ValueResolver;

use Pentiminax\UX\SweetAlert\Model\Result;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;

final class ResultValueResolver implements ValueResolverInterface
{
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        if (Result::class !== $argument->getType()) {
            return [];
        }

        $content = $request->getContent();

        if ('' === $content) {
            return [];
        }

        $data = json_decode($content, true);

        if (!\is_array($data)) {
            return [];
        }

        return [Result::fromArray($data)];
    }
}
