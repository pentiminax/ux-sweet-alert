<?php

namespace Pentiminax\UX\SweetAlert\Twig\Extension;

use Pentiminax\UX\SweetAlert\ToastManager;
use Symfony\UX\StimulusBundle\Helper\StimulusHelper;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

class ToastExtension extends AbstractExtension
{
    public function __construct(
        private readonly Environment $twig,
        private readonly ToastManager $toastManager,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('ux_toast_scripts', [$this, 'scripts']),
        ];
    }

    public function scripts(): Markup
    {
        $stimulus = new StimulusHelper($this->twig);
        $toasts = $this->toastManager->getToasts();

        $controllers['@pentiminax/ux-sweet-alert/sweetalert'] = [
            'view' => $toasts
        ];

        $stimulusAttributes = $stimulus->createStimulusAttributes();
        foreach ($controllers as $name => $controllerValues) {
            $stimulusAttributes->addController($name, $controllerValues);
        }

        $html = \sprintf('<div %s></div>', $stimulusAttributes);

        return new Markup($html, 'UTF-8');
    }
}