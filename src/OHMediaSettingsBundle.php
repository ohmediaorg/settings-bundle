<?php

namespace OHMedia\SettingsBundle;

use OHMedia\SettingsBundle\DependencyInjection\Compiler\SettingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OHMediaSettingsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new SettingsPass());
    }
}
