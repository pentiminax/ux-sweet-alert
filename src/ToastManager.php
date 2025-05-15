<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Toast;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class ToastManager implements ToastManagerInterface
{
    private array $toasts = [];

    public function __construct(
        private readonly RequestStack $requestStack
    ) {
    }

    public function addToast(Toast $toast): void
    {
        $this->toasts[] = $toast;

        $this->getSession()->getFlashBag()->set('ux-sweet-alert:toasts', $this->toasts);
    }

    public function success(string $id, string $title, string $text, Position $position, bool $showConfirmButton): void
    {
        $toast = Toast::new($id, $title, $text);

        $toast->position($position);

        if (!$showConfirmButton) {
            $toast->withoutConfirmButton();
        }

        $this->addToast($toast);
    }

    public function getToasts(): array
    {
        return $this->getSession()->getFlashBag()->get('ux-sweet-alert:toasts');
    }

    public function getSession(): FlashBagAwareSessionInterface
    {
        return $this->requestStack->getSession();
    }
}