<?php

namespace OHMedia\SettingsBundle\Twig;

use OHMedia\SettingsBundle\Service\Settings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsExtension extends AbstractExtension
{
    public function __construct(private Settings $settings)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('setting', [$this, 'getSetting']),
        ];
    }

    public function getSetting(string $setting): mixed
    {
        return $this->settings->get($setting);
    }
}
