<?php

namespace App\Twig\Extension;

use App\Toast\ToastManager;
use Symfony\UX\StimulusBundle\Helper\StimulusHelper;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
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

    public function scripts(): string
    {
        $stimulus = new StimulusHelper($this->twig);
        $toasts = $this->toastManager->getToasts();

        $controllers['toast'] = [
            'view' => $toasts
        ];

        $stimulusAttributes = $stimulus->createStimulusAttributes();
        foreach ($controllers as $name => $controllerValues) {
            $stimulusAttributes->addController($name, $controllerValues);
        }

        return \sprintf('<div %s></div>', $stimulusAttributes);
    }
}