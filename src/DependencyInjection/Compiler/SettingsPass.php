<?php

namespace JstnThms\SettingsBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class SettingsPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        // always first check if the primary service is defined
        if (!$container->has('jstnthms_settings.settings')) {
            return;
        }

        $definition = $container->findDefinition('jstnthms_settings.settings');

        $tagged = $container->findTaggedServiceIds('jstnthms_settings.transformer');

        foreach ($tagged as $id => $tags) {
            $definition->addMethodCall('addTransformer', [new Reference($id)]);
        }
    }
}
