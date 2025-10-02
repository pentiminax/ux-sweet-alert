<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Pentiminax\UX\SweetAlert\AlertManager;
use Pentiminax\UX\SweetAlert\AlertManagerInterface;
use Pentiminax\UX\SweetAlert\Context\SweetAlertContext;
use Pentiminax\UX\SweetAlert\Context\SweetAlertContextInterface;
use Pentiminax\UX\SweetAlert\EventListener\RenderAlertListener;
use Pentiminax\UX\SweetAlert\DataCollector\SweetAlertDataCollector;
use Pentiminax\UX\SweetAlert\FlashMessageConverter;
use Pentiminax\UX\SweetAlert\FlashMessageConverterInterface;
use Pentiminax\UX\SweetAlert\ToastManager;
use Pentiminax\UX\SweetAlert\ToastManagerInterface;
use Pentiminax\UX\SweetAlert\Twig\Components\ConfirmButton;
use Pentiminax\UX\SweetAlert\Twig\Extension\AlertExtension;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\LiveComponent\LiveResponder;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sweet_alert.alert_manager', AlertManager::class)
        ->arg('$requestStack', new Reference('request_stack'))
        ->arg('$context', new Reference('sweet_alert.context'))
        ->arg('$flashMessageConverter', new Reference('sweet_alert.flash_message_converter'))
        ->arg('$autoConvertFlashMessages', '%sweet_alert.auto_convert_flash_messages%')
        ->private();

    $services
        ->alias(AlertManagerInterface::class, 'sweet_alert.alert_manager')
        ->private();

    $services
        ->set('sweet_alert.toast_manager', ToastManager::class)
        ->arg('$requestStack', new Reference('request_stack'))
        ->arg('$context', new Reference('sweet_alert.context'))
        ->private();

    $services
        ->alias(ToastManagerInterface::class, 'sweet_alert.toast_manager')
        ->private();

    $services
        ->set('sweet_alert.twig_extension', AlertExtension::class)
        ->arg('$twig', new Reference('twig'))
        ->arg('$alertManager', new Reference('sweet_alert.alert_manager'))
        ->arg('$toastManager', new Reference('sweet_alert.toast_manager'))
        ->tag('twig.extension')
        ->private();

    $services
        ->set(ConfirmButton::class)
        ->call('setLiveResponder', [service(LiveResponder::class)])
        ->call('setContext', [service(SweetAlertContextInterface::class)])
        ->call('setTranslator', [service(TranslatorInterface::class)])
        ->tag('twig.component', [
            'key' => 'SweetAlert:ConfirmButton',
            'expose_public_props' => true,
            'attributes_var' => 'attributes',
            'live' => true,
            'route' => 'ux_live_component',
            'method' => 'post',
            'url_reference_type' => true,
        ])
        ->tag('controller.service_arguments')
        ->public();

    $services
        ->alias(SweetAlertContextInterface::class, 'sweet_alert.context')
        ->private();

    $services
        ->set('sweet_alert.context', SweetAlertContext::class)
        ->tag('kernel.reset', ['method' => 'reset'])
        ->share();

    $services
        ->set(SweetAlertDataCollector::class)
        ->arg('$context', new Reference('sweet_alert.context'))
        ->tag('data_collector', ['id' => 'ux_sweetalert', 'template' => '@SweetAlert/collector/data_collector.html.twig']);

    $services
        ->set('sweet_alert.flash_message_converter', FlashMessageConverter::class);

    $services
        ->alias(FlashMessageConverterInterface::class, 'sweet_alert.flash_message_converter')
        ->private();

    if (class_exists(\Symfony\UX\Turbo\TurboBundle::class)) {
        $services
            ->set(RenderAlertListener::class)
            ->arg('$alertManager', new Reference('sweet_alert.alert_manager'))
            ->arg('$toastManager', new Reference('sweet_alert.toast_manager'))
            ->arg('$twig', new Reference('twig'))
            ->tag('kernel.event_listener', ['event' => 'kernel.response']);
    }
};
