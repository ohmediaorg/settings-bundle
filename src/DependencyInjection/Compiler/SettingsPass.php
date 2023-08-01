<?php

namespace OHMedia\SettingsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SettingsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('oh_media_settings.settings')) {
            return;
        }

        $definition = $container->findDefinition('oh_media_settings.settings');

        $tagged = $container->findTaggedServiceIds('oh_media_settings.transformer');

        foreach ($tagged as $id => $tags) {
            $definition->addMethodCall('addTransformer', [new Reference($id)]);
        }
    }
}
