<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Fixtures;

trait BrowserEventsTrait
{
    private function browserEvents(object $component): array
    {
        $reflection = new \ReflectionClass($component);

        do {
            if ($reflection->hasProperty('liveResponder')) {
                $property = $reflection->getProperty('liveResponder');
                $property->setAccessible(true);

                return $property->getValue($component)->getBrowserEventsToDispatch();
            }
            $reflection = $reflection->getParentClass();
        } while (false !== $reflection);

        throw new \RuntimeException('Could not find liveResponder property on '.$component::class);
    }
}
