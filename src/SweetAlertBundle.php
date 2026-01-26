<?php

namespace Pentiminax\UX\SweetAlert;

use Pentiminax\UX\SweetAlert\Enum\Position;
use Pentiminax\UX\SweetAlert\Enum\Theme;
use Symfony\Component\AssetMapper\AssetMapperInterface;
use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SweetAlertBundle extends AbstractBundle
{
    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->scalarNode('auto_convert_flash_messages')->defaultFalse()->end()
                ->enumNode('theme')->values(array_map(static fn (Theme $theme) => $theme->value, Theme::cases()))->defaultValue('auto')->end()
                ->arrayNode('default_options')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->enumNode('position')
                            ->values(array_map(static fn (Position $position) => $position->value, Position::cases()))
                            ->defaultValue('center')
                        ->end()
                        ->scalarNode('confirmButtonColor')->defaultValue('#3085d6')->end()
                        ->scalarNode('confirmButtonText')->defaultValue('OK')->end()
                        ->scalarNode('cancelButtonText')->defaultValue('Cancel')->end()
                        ->scalarNode('denyButtonText')->defaultValue('No')->end()
                        ->booleanNode('showConfirmButton')->defaultTrue()->end()
                        ->booleanNode('showCancelButton')->defaultFalse()->end()
                        ->booleanNode('showDenyButton')->defaultFalse()->end()
                        ->booleanNode('animation')->defaultTrue()->end()
                        ->booleanNode('backdrop')->defaultTrue()->end()
                        ->booleanNode('allowOutsideClick')->defaultTrue()->end()
                        ->booleanNode('allowEscapeKey')->defaultTrue()->end()
                        ->booleanNode('focusConfirm')->defaultTrue()->end()
                        ->booleanNode('draggable')->defaultFalse()->end()
                        ->booleanNode('topLayer')->defaultFalse()->end()
                        ->integerNode('timer')->defaultNull()->end()
                        ->booleanNode('timerProgressBar')->defaultFalse()->end()
                        ->arrayNode('customClass')
                            ->scalarPrototype()->end()
                            ->defaultValue([])
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }

    public function loadExtension(array $config, ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->setParameter('sweet_alert.auto_convert_flash_messages', $config['auto_convert_flash_messages']);
        $builder->setParameter('sweet_alert.theme', $config['theme']);
        $builder->setParameter('sweet_alert.default_options', $config['default_options']);

        $container->import('../config/services.php');
    }

    public function prependExtension(ContainerConfigurator $container, ContainerBuilder $builder): void
    {
        $builder->prependExtensionConfig('twig_component', [
            'defaults' => [
                'Pentiminax\UX\SweetAlert\Twig\Components\\' => [
                    'template_directory' => '@SweetAlert/components/',
                    'name_prefix'        => 'SweetAlert',
                ],
            ],
        ]);

        if ($this->isAssetMapperAvailable($builder)) {
            $builder->prependExtensionConfig('framework', [
                'asset_mapper' => [
                    'paths' => [
                        __DIR__.'/../assets/dist' => '@pentiminax/ux-sweetalert',
                    ],
                ],
            ]);
        }
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
