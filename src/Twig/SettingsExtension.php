<?php

namespace OHMedia\SettingsBundle\Twig;

use OHMedia\SettingsBundle\Service\Settings;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class SettingsExtension extends AbstractExtension
{
    private $settings;

    public function __construct(Settings $settings)
    {
        $this->settings = $settings;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('oh_media_setting', [$this, 'getSetting'])
        ];
    }

    public function getSetting(string $setting): mixed
    {
        return $this->settings->get($setting);
    }
}
