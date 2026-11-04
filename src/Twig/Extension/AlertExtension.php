<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Twig\Extension;

use Pentiminax\UX\SweetAlert\Delivery\AlertDelivery;
use Symfony\UX\StimulusBundle\Helper\StimulusHelper;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFunction;

final class AlertExtension extends AbstractExtension
{
    private const CONTROLLER = '@pentiminax/ux-sweet-alert/sweetalert';
    private const HTML       = '<div data-turbo-temporary="true" %s></div>';

    public function __construct(
        private readonly Environment $twig,
        private readonly AlertDelivery $delivery,
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

        $stimulusAttributes = $stimulus->createStimulusAttributes();
        $stimulusAttributes->addController(self::CONTROLLER, ['view' => $this->delivery->forView()]);

        $html = \sprintf(self::HTML, $stimulusAttributes);

        return new Markup($html, 'UTF-8');
    }
}
