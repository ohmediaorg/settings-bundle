<?php

namespace OHMedia\SettingsBundle\DependencyInjection\Compiler;

use OHMedia\SettingsBundle\Service\Settings;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SettingsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        // always first check if the primary service is defined
        if (!$container->has(Settings::class)) {
            return;
        }

        $definition = $container->findDefinition(Settings::class);

        $tagged = $container->findTaggedServiceIds('oh_media_settings.transformer');

        foreach ($tagged as $id => $tags) {
            $definition->addMethodCall('addTransformer', [new Reference($id)]);
        }
    }
}
