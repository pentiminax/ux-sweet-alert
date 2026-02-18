<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Pentiminax\UX\SweetAlert\Model\AlertDefaults;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AlertManager implements AlertManagerInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly SweetAlertContextInterface $context,
        private readonly FlashMessageConverter $flashMessageConverter,
        private readonly AlertDefaults $alertDefaults,
        private readonly bool $autoConvertFlashMessages = false,
    ) {
    }

    public function addAlert(Alert $alert): void
    {
        $session  = $this->getSession();
        $alerts   = $session->get(AlertManagerInterface::ALERT_STORAGE_KEY, []);
        $alerts[] = $alert;

        $session->set(AlertManagerInterface::ALERT_STORAGE_KEY, $alerts);
        $this->context->addAlert($alert);
    }

    /**
     * @return Alert[]
     */
    public function getAlerts(): array
    {
        $session = $this->getSession();

        // Consume alerts from session attribute (consume-on-read)
        $storedAlerts = $session->get(AlertManagerInterface::ALERT_STORAGE_KEY, []);
        $session->remove(AlertManagerInterface::ALERT_STORAGE_KEY);

        if (!$this->autoConvertFlashMessages) {
            return $storedAlerts;
        }

        $alerts = [$storedAlerts];

        foreach ($this->getSession()->getFlashBag()->peekAll() as $key => $messages) {
            $alerts[] = $this->flashMessageConverter->convert($key, $messages);
            $this->getSession()->getFlashBag()->get($key);
        }

        return array_merge(...$alerts);
    }

    public function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }

    public function success(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::CENTER,
        ?Theme $theme = null,
        array $customClass = [],
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert {
        return $this->createAndAddAlert($id, $title, $text, $position, $theme, Icon::SUCCESS, $customClass, $toast, $timer, $timerProgressBar);
    }

    public function error(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::CENTER,
        ?Theme $theme = null,
        array $customClass = [],
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert {
        return $this->createAndAddAlert($id, $title, $text, $position, $theme, Icon::ERROR, $customClass, $toast, $timer, $timerProgressBar);
    }

    public function warning(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::CENTER,
        ?Theme $theme = null,
        array $customClass = [],
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert {
        return $this->createAndAddAlert($id, $title, $text, $position, $theme, Icon::WARNING, $customClass, $toast, $timer, $timerProgressBar);
    }

    public function info(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::CENTER,
        ?Theme $theme = null,
        array $customClass = [],
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert {
        return $this->createAndAddAlert($id, $title, $text, $position, $theme, Icon::INFO, $customClass, $toast, $timer, $timerProgressBar);
    }

    public function question(
        string $title,
        string $id = '',
        string $text = '',
        Position $position = Position::CENTER,
        ?Theme $theme = null,
        array $customClass = [],
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert {
        return $this->createAndAddAlert($id, $title, $text, $position, $theme, Icon::QUESTION, $customClass, $toast, $timer, $timerProgressBar);
    }

    public function toast(
        string $title,
        string $id = '',
        string $text = '',
        ?Icon $icon = Icon::SUCCESS,
        Position $position = Position::BOTTOM_END,
        ?Theme $theme = null,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert {
        return $this->createAndAddAlert(
            id: $id,
            title: $title,
            text: $text,
            position: $position,
            theme: $theme,
            icon: $icon,
            toast: true,
            timer: $timer,
            timerProgressBar: $timerProgressBar
        );
    }

    private function createAndAddAlert(
        string $id,
        string $title,
        string $text,
        Position $position,
        ?Theme $theme,
        ?Icon $icon,
        array $customClass = [],
        bool $toast = false,
        ?int $timer = null,
        bool $timerProgressBar = false,
    ): Alert {
        $id = empty($id) ? uniqid(more_entropy: true) : $id;

        // Apply toast defaults if toast mode
        if ($toast && Position::CENTER === $position) {
            $position = Position::BOTTOM_END;
        }

        $alert = Alert::withDefaults(
            defaults: $this->alertDefaults,
            title: $title,
            id: $id,
            text: $text,
            icon: $icon,
            position: $position,
            customClass: $customClass
        );

        if ($toast) {
            $alert->asToast();
            $alert->timer($timer);
            $alert->withoutConfirmButton();

            if ($timerProgressBar) {
                $alert->withTimerProgressBar();
            }
        }

        $this->addAlert($alert);

        return $alert;
    }
}
