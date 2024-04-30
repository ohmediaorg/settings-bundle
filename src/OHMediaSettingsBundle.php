<?php

namespace OHMedia\SettingsBundle;

use OHMedia\SettingsBundle\DependencyInjection\Compiler\SettingsPass;
use OHMedia\SettingsBundle\Interfaces\TransformerInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class OHMediaSettingsBundle extends AbstractBundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new SettingsPass());
    }

    public function loadExtension(
        array $config,
        ContainerConfigurator $containerConfigurator,
        ContainerBuilder $containerBuilder
    ): void {
        $containerConfigurator->import('../config/services.yaml');

        $containerBuilder->registerForAutoconfiguration(TransformerInterface::class)
            ->addTag('oh_media_settings.transformer')
        ;
    }
}
