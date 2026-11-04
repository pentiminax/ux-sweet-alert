<?php

declare(strict_types=1);

namespace App\Twig\Extension;

use App\Examples;
use App\Page;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class DemoExtension extends AbstractExtension
{
    public function getFunctions(): array
    {
        return [
            new TwigFunction('demo_pages', Page::grouped(...)),
            new TwigFunction('demo_examples', static fn (string $page): array => array_keys(Examples::for($page))),
            new TwigFunction('example_code', $this->exampleCode(...)),
            new TwigFunction('method_code', $this->methodCode(...)),
        ];
    }

    /**
     * The closure body, without its signature and closing brace.
     */
    private function exampleCode(string $page, string $example): string
    {
        $closure = Examples::for($page)[$example] ?? throw new \InvalidArgumentException(\sprintf('Unknown example "%s/%s".', $page, $example));

        return $this->source(new \ReflectionFunction($closure), trim: 1);
    }

    /**
     * @param string $method "Class::method"
     */
    private function methodCode(string $method): string
    {
        return $this->source(\ReflectionMethod::createFromMethodName($method), trim: 0);
    }

    private function source(\ReflectionFunctionAbstract $function, int $trim): string
    {
        $file  = file((string) $function->getFileName(), \FILE_IGNORE_NEW_LINES) ?: [];
        $start = (int) $function->getStartLine() - 1 + $trim;
        $lines = \array_slice($file, $start, (int) $function->getEndLine() - $start - $trim);

        $indents = array_map(
            static fn (string $line): int => \strlen($line) - \strlen(ltrim($line)),
            array_filter($lines, static fn (string $line): bool => '' !== trim($line)),
        );
        $indent = [] === $indents ? 0 : min($indents);

        return implode("\n", array_map(static fn (string $line): string => substr($line, $indent), $lines));
    }
}
