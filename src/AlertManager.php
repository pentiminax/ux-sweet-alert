<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\Enum\Icon;
use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Model\Alert;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;

class AlertManager implements AlertManagerInterface
{
    private array $alerts = [];

    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly SweetAlertContextInterface $context,
        private readonly FlashMessageConverter $flashMessageConverter,
        private readonly bool $autoConvertFlashMessages = false,
    ) {
    }

    public function addAlert(Alert $alert): void
    {
        $this->alerts[] = $alert;

        $this->getSession()->getFlashBag()->set('ux-sweet-alert:alerts', $this->alerts);
        $this->context->addAlert($alert);
    }

    /**
     * @return Alert[]
     */
    public function getAlerts(): array
    {
        $alerts = [];
        if ($this->autoConvertFlashMessages) {
            foreach ($this->getFlashBag()->peekAll() as $key => $messages) {
                if ($key === ToastManagerInterface::TOAST_STORAGE_KEY) {
                    continue;
                }

                if ($key === AlertManagerInterface::ALERT_STORAGE_KEY) {
                    $alerts[] = $this->getFlashBag()->get($key);
                } else {
                    $alerts[] = $this->flashMessageConverter->convert($key, $messages);
                    $this->getFlashBag()->get($key);
                }
            }
        } else {
            return $this->getFlashBag()->get(self::ALERT_STORAGE_KEY);
        }

        return array_merge(...$alerts);
    }

    public function getSession(): FlashBagAwareSessionInterface
    {
        return $this->requestStack->getSession();
    }

    public function success(string $title, string $id = '', string $text = '', Position $position = Position::CENTER, array $customClass = []): Alert
    {
        return $this->createAndAddAlert($id, $title, $text, $position, Icon::SUCCESS, $customClass);
    }

    public function error(string $title, string $id = '', string $text = '', Position $position = Position::CENTER, array $customClass = []): Alert
    {
        return $this->createAndAddAlert($id, $title, $text, $position, Icon::ERROR, $customClass);
    }

    public function warning(string $title, string $id = '', string $text = '', Position $position = Position::CENTER, array $customClass = []): Alert
    {
        return $this->createAndAddAlert($id, $title, $text, $position, Icon::WARNING, $customClass);
    }

    public function info(string $title, string $id = '', string $text = '', Position $position = Position::CENTER, array $customClass = []): Alert
    {
        return $this->createAndAddAlert($id, $title, $text, $position, Icon::INFO, $customClass);
    }

    public function question(string $title, string $id = '', string $text = '', Position $position = Position::CENTER, array $customClass = []): Alert
    {
        return $this->createAndAddAlert($id, $title, $text, $position, Icon::QUESTION, $customClass);
    }

    private function createAndAddAlert(
        string $id,
        string $title,
        string $text,
        Position $position,
        Icon $icon,
        array $customClass = []
    ): Alert {
        $id = empty($id) ? uniqid(more_entropy: true) : $id;

        $alert = Alert::new(
            title: $title,
            id: $id,
            text: $text,
            icon: $icon,
            position: $position,
            customClass: $customClass
        );

        $this->addAlert($alert);

        return $alert;
    }

    private function getFlashBag(): FlashBagInterface
    {
        return $this->getSession()->getFlashBag();
    }
}