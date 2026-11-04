<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Todo\TodoStore;
use Pentiminax\UX\SweetAlert\Twig\Components\ConfirmButton;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveArg;

#[AsLiveComponent(template: '@SweetAlert/components/ConfirmButton.html.twig')]
final class DeleteTodoButton extends ConfirmButton
{
    public function __construct(private readonly TodoStore $todos)
    {
    }

    /**
     * $args holds the data-live-item-*-param attributes of the button.
     *
     * @param array<string, mixed> $result
     * @param array<string, mixed> $args
     */
    #[LiveAction]
    public function callbackAction(#[LiveArg] array $result, #[LiveArg] array $args = []): mixed
    {
        parent::callbackAction($result, $args);

        if ($this->result?->isConfirmed) {
            $this->todos->remove((int) $args['id']);
            $this->emit(TodoList::CHANGED_EVENT);
        }

        return null;
    }
}
