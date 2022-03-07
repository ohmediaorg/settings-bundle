<?php

namespace JstnThms\SettingsBundle;

use JstnThms\SettingsBundle\DependencyInjection\Compiler\SettingsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class JstnThmsSettingsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        
        $container->addCompilerPass(new SettingsPass());
    }
}
