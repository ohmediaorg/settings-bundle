<?php

namespace OHMedia\SettingsBundle\Twig;

use OHMedia\SettingsBundle\Settings\Settings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsExtension extends AbstractExtension
{
    private $settings;
    
    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }
    
    public function getFunctions()
    {
        return [
            new TwigFunction('ohmedia_setting', [$this, 'getSetting'])
        ];
    }
    
    public function getSetting($setting)
    {
        return $this->settings->get($setting);
    }
}
