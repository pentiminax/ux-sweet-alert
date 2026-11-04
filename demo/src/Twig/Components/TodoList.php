<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Todo\TodoStore;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveListener;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent]
final class TodoList
{
    use DefaultActionTrait;

    public const CHANGED_EVENT = 'todos:changed';

    public function __construct(private readonly TodoStore $todos)
    {
    }

    /**
     * @return array<int, string>
     */
    public function getTodos(): array
    {
        return $this->todos->all();
    }

    #[LiveAction]
    public function reset(): void
    {
        $this->todos->reset();
    }

    // Re-rendering is all it takes: the children already changed the store.
    #[LiveListener(self::CHANGED_EVENT)]
    public function refresh(): void
    {
    }
}
