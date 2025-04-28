<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class ToastManager implements ToastManagerInterface
{
    private array $toasts = [];

    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function addToast(string $title, string $text, string $icon, Position $position = Position::CENTER): void
    {
        $this->toasts[] = [
            'title' => $title,
            'text' => $text,
            'icon' => $icon,
            'position' => $position->value,
            'toast' => true,
        ];

        $this->getSession()->getFlashBag()->set('ux-toast:toasts', $this->toasts);
    }

    public function getToasts(): array
    {
        return $this->getSession()->getFlashBag()->get('ux-toast:toasts');
    }

    public function getSession(): FlashBagAwareSessionInterface
    {
        return $this->requestStack->getSession();
    }

    public function success(string $title, string $text, Position $position = Position::CENTER): void
    {
        $this->addToast($title, $text, 'success', $position);
    }
}