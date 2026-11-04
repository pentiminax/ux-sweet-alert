<?php

declare(strict_types=1);

namespace App\Todo;

use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Session-backed list so the Live Components demo needs no database.
 */
final class TodoStore
{
    private const SESSION_KEY = 'demo.todos';
    private const SEED        = [1 => 'Read the docs', 2 => 'Try every alert', 3 => 'Star the repository'];

    public function __construct(private readonly RequestStack $requestStack)
    {
    }

    /**
     * @return array<int, string>
     */
    public function all(): array
    {
        return $this->requestStack->getSession()->get(self::SESSION_KEY, self::SEED);
    }

    public function rename(int $id, string $label): void
    {
        $todos = $this->all();
        if (isset($todos[$id])) {
            $todos[$id] = $label;
            $this->save($todos);
        }
    }

    public function remove(int $id): void
    {
        $todos = $this->all();
        unset($todos[$id]);
        $this->save($todos);
    }

    public function reset(): void
    {
        $this->save(self::SEED);
    }

    /**
     * @param array<int, string> $todos
     */
    private function save(array $todos): void
    {
        $this->requestStack->getSession()->set(self::SESSION_KEY, $todos);
    }
}
