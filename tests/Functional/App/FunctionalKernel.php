<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests\Functional\App;

use Pentiminax\UX\SweetAlert\Tests\Kernel;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

/**
 * Test application exercising the bundle end to end: real container, real HTTP kernel, real session.
 */
final class FunctionalKernel extends Kernel
{
    /**
     * @param array<string, mixed> $sweetAlertConfig
     */
    public function __construct(private readonly array $sweetAlertConfig = [])
    {
        parent::__construct('test', true);
    }

    public function getCacheDir(): string
    {
        // One container per configuration, so tests with different bundle config never share a cache.
        return $this->getProjectDir().'/var/cache/functional/'.md5(serialize($this->sweetAlertConfig));
    }

    public function getLogDir(): string
    {
        return $this->getProjectDir().'/var/log';
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        parent::configureContainer($container);

        $container->extension('framework', [
            'session' => ['storage_factory_id' => 'session.storage.factory.mock_file'],
            'router'  => ['utf8' => true],
        ]);

        $container->extension('sweet_alert', $this->sweetAlertConfig);

        $container->services()
            ->set(AlertController::class)
            ->autowire()
            ->tag('controller.service_arguments')
            ->public();

        $container->services()
            ->set(AlertQueuedRecorder::class)
            ->autoconfigure()
            ->public();
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        foreach (AlertController::ROUTES as $path => $action) {
            $routes->add($action, $path)->controller([AlertController::class, $action]);
        }
    }
}
