<?php

namespace Pentiminax\UX\SweetAlert\Twig\Extension;

use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\ToastManagerInterface;
use Symfony\UX\StimulusBundle\Helper\StimulusHelper;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class AlertExtension extends AbstractExtension
{
    public function __construct(
        private readonly Environment  $twig,
        private readonly AlertManagerInterface $alertManager,
        private readonly ToastManagerInterface $toastManager,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ux_sweet_alert_scripts', [$this, 'scripts']),
        ];
    }

    public function scripts(): Markup
    {
        $stimulus = new StimulusHelper($this->twig);
        $alerts = $this->alertManager->getAlerts();
        $toasts = $this->toastManager->getToasts();

        $controllers['@pentiminax/ux-sweet-alert/sweetalert'] = [
            'view' => array_merge($alerts, $toasts),
        ];

        $stimulusAttributes = $stimulus->createStimulusAttributes();
        foreach ($controllers as $name => $controllerValues) {
            $stimulusAttributes->addController($name, $controllerValues);
        }

        $html = \sprintf('<div %s></div>', $stimulusAttributes);

        return new Markup($html, 'UTF-8');
    }
}