<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Twig\Extension\AlertExtension;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;


class SweetAlertBundle extends AbstractBundle
{
    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $container->services()
            ->set('sweet_alert.alert_manager', AlertManager::class)
            ->arg(0, new Reference('request_stack'))
            ->private();

        $container->services()
            ->alias(AlertManagerInterface::class, 'sweet_alert.alert_manager')
            ->private();

        $container->services()
            ->set('sweet_alert.toast_manager', ToastManager::class)
            ->arg(0, new Reference('request_stack'))
            ->private();

        $container->services()
            ->alias(ToastManagerInterface::class, 'sweet_alert.toast_manager')
            ->private();

        $container->services()
            ->set('sweet_alert.twig_extension', AlertExtension::class)
            ->arg(0, new Reference('twig'))
            ->arg(1, new Reference('sweet_alert.alert_manager'))
            ->arg(2, new Reference('sweet_alert.toast_manager'))
            ->tag('twig.extension')
            ->private();
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        if (!$this->isAssetMapperAvailable($builder)) {
            return;
        }

        $builder->prependExtensionConfig('framework', [
            'asset_mapper' => [
                'paths' => [
                    __DIR__.'/../assets/dist' => '@pentiminax/ux-sweetalert',
                ],
            ],
        ]);
    }

    private function isAssetMapperAvailable(ContainerBuilder $builder): bool
    {
        if (!interface_exists(AssetMapperInterface::class)) {
            return false;
        }

        $bundlesMetadata = $builder->getParameter('kernel.bundles_metadata');
        if (!isset($bundlesMetadata['FrameworkBundle'])) {
            return false;
        }

        return is_file($bundlesMetadata['FrameworkBundle']['path'].'/Resources/config/asset_mapper.php');
    }
}
