<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Toast;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class ToastManager implements ToastManagerInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly SweetAlertContextInterface $context
    ) {
    }

    public function addToast(Toast $toast): void
    {
        $toasts = $this->getSession()->getFlashBag()->peek(ToastManagerInterface::TOAST_STORAGE_KEY, []);
        $toasts[] = $toast;

        $this->getSession()->getFlashBag()->set(ToastManagerInterface::TOAST_STORAGE_KEY, $toasts);

        $this->context->addToast($toast);
    }

    public function success(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Toast {
        return $this->createAndAddToast($id, $title, $text, $position, $showConfirmButton, Icon::SUCCESS, $timer, $timerProgressBar);
    }

    public function error(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Toast {
        return $this->createAndAddToast($id, $title, $text, $position, $showConfirmButton, Icon::ERROR, $timer, $timerProgressBar);
    }

    public function warning(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Toast {
        return $this->createAndAddToast($id, $title, $text, $position, $showConfirmButton, Icon::WARNING, $timer, $timerProgressBar);
    }

    public function info(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Toast {
        return $this->createAndAddToast($id, $title, $text, $position, $showConfirmButton, Icon::INFO, $timer, $timerProgressBar);
    }

    public function question(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::BOTTOM_END,
        bool $showConfirmButton = false,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Toast {
        return $this->createAndAddToast($id, $title, $text, $position, $showConfirmButton, Icon::INFO, $timer, $timerProgressBar);
    }

    private function createAndAddToast(
        string $id,
        string $title,
        string $text,
        Position $position,
        bool $showConfirmButton,
        Icon $icon,
        ?int $timer = null,
        bool $timerProgressBar = false
    ): Toast {
        $id = empty($id) ? uniqid(more_entropy: true) : $id;

        $toast = Toast::new(
            title: $title,
            id: $id,
            text: $text,
            icon: $icon,
            position: $position
        );

        $toast
            ->position($position)
            ->timer($timer);

        if (!$showConfirmButton) {
            $toast->withoutConfirmButton();
        }

        if ($timerProgressBar) {
            $toast->withTimerProgressBar();
        }

        $this->addToast($toast);

        return $toast;
    }

    public function getToasts(): array
    {
        return $this->getSession()->getFlashBag()->get(ToastManagerInterface::TOAST_STORAGE_KEY);
    }

    public function getSession(): FlashBagAwareSessionInterface
    {
        return $this->requestStack->getSession();
    }
}