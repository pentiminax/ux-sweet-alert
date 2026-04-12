<?php

declare(strict_types=1);

namespace Pentiminax\UX\SweetAlert\Tests;

use Pentiminax\UX\SweetAlert\SweetAlertBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\UX\LiveComponent\LiveComponentBundle;
use Symfony\UX\StimulusBundle\StimulusBundle;
use Symfony\UX\TwigComponent\TwigComponentBundle;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public function registerBundles(): array
    {
        return [
            new FrameworkBundle(),
            new StimulusBundle(),
            new TwigBundle(),
            new TwigComponentBundle(),
            new LiveComponentBundle(),
            new SweetAlertBundle(),
        ];
    }

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->extension('framework', [
            'test'   => true,
            'secret' => 'test-secret',
        ]);

        $container->extension('twig', [
            'default_path' => '%kernel.project_dir%/templates',
        ]);
    }
}
