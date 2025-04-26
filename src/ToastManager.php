<?php

namespace App\Toast;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class ToastManager
{
    private array $toasts = [];

    public function __construct(
        private RequestStack $requestStack
    ) {
    }

    public function addToast(string $title, string $text, string $icon): void
    {
        $this->toasts[] = [
            'title' => $title,
            'text' => $text,
            'icon' => $icon,
        ];

        $this->getSession()->getFlashBag()->set('ux-toast:toasts', $this->toasts);
    }

    public function getToasts(): array
    {
        return $this->getSession()->getFlashBag()->get('ux-toast:toasts');
    }

    public function getSession(): FlashBagAwareSessionInterface
    {
        $session = $this->requestStack->getSession();

        if (!$session instanceof FlashBagAwareSessionInterface) {
            throw new \LogicException(\sprintf('Cclass "%s" doesn\'t implement "%s".', get_debug_type($session), FlashBagAwareSessionInterface::class));
        }

        return $session;
    }
}