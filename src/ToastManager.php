<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Icon;
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

    public function success(
        string $id,
        string $title,
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null
    ): Toast {
        return $this->createAndAddToast($id, $title, $text, $position, $showConfirmButton, Icon::SUCCESS, $timer);
    }

    public function error(
        string $id,
        string $title,
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null
    ): Toast {
        return $this->createAndAddToast($id, $title, $text, $position, $showConfirmButton, Icon::ERROR, $timer);
    }

    public function warning(
        string $id,
        string $title,
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null
    ): Toast {
        return $this->createAndAddToast($id, $title, $text, $position, $showConfirmButton, Icon::WARNING, $timer);
    }

    public function info(
        string $id,
        string $title,
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null
    ): Toast {
        return $this->createAndAddToast($id, $title, $text, $position, $showConfirmButton, Icon::INFO, $timer);
    }

    public function question(
        string $id,
        string $title,
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null
    ): Toast {
        return $this->createAndAddToast($id, $title, $text, $position, $showConfirmButton, Icon::INFO, $timer);
    }

    private function createAndAddToast(
        string $id,
        string $title,
        string $text,
        Position $position,
        bool $showConfirmButton,
        Icon $icon,
        ?int $timer = null
    ): Toast {
        $toast = Toast::new($id, $title, $text, $icon, $position);

        $toast
            ->position($position)
            ->timer($timer);

        if (!$showConfirmButton) {
            $toast->withoutConfirmButton();
        }

        $this->addToast($toast);

        return $toast;
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