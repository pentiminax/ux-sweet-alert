<?php

declare(strict_types=1);

namespace App\Twig\Components;

use App\Todo\TodoStore;
use Pentiminax\UX\SweetAlert\Model\Result;
use Pentiminax\UX\SweetAlert\Twig\Components\InputModal;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;

#[AsLiveComponent(template: '@SweetAlert/components/InputModal.html.twig')]
final class RenameTodoModal extends InputModal
{
    public function __construct(private readonly TodoStore $todos)
    {
    }

    /**
     * @param array<string, mixed> $args
     */
    protected function onResult(Result $result, array $args = []): void
    {
        $label = \is_string($result->value) ? trim($result->value) : '';

        if ($result->isConfirmed && '' !== $label) {
            $this->todos->rename((int) $args['id'], $label);
            $this->emit(TodoList::CHANGED_EVENT);
        }
    }
}
