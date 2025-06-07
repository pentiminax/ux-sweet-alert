<?php

use Pentiminax\UX\SweetAlert\AlertManager;
use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\ToastManager;
use Pentiminax\UX\SweetAlert\ToastManagerInterface;
use Pentiminax\UX\SweetAlert\Twig\Components\ConfirmButton;
use Pentiminax\UX\SweetAlert\Twig\Extension\AlertExtension;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sweet_alert.alert_manager', AlertManager::class)
        ->arg(0, new Reference('request_stack'))
        ->private();

    $services
        ->alias(AlertManagerInterface::class, 'sweet_alert.alert_manager')
        ->private();

    $services
        ->set('sweet_alert.toast_manager', ToastManager::class)
        ->arg(0, new Reference('request_stack'))
        ->private();

    $services
        ->alias(ToastManagerInterface::class, 'sweet_alert.toast_manager')
        ->private();

    $services
        ->set('sweet_alert.twig_extension', AlertExtension::class)
        ->arg(0, new Reference('twig'))
        ->arg(1, new Reference('sweet_alert.alert_manager'))
        ->arg(2, new Reference('sweet_alert.toast_manager'))
        ->tag('twig.extension')
        ->private();

    $services
        ->set(ConfirmButton::class)
        ->tag('twig_component', [
            'key' => 'Pentiminax:UxSweetAlert:ConfirmButton',
            'expose_public_props' => true,
            'live' => true,
            'route' => 'ux_live_component',
            'method' => 'post',
            'url_reference_type' => true,
        ]);
};
