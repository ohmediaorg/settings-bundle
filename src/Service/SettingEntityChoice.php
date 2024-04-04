<?php

namespace OHMedia\SettingsBundle\Service;

use OHMedia\SettingsBundle\Entity\Setting;

class SettingEntityChoice implements EntityChoiceInterface
{
    public function getLabel(): string
    {
        return 'Settings';
    }

    public function getEntities(): array
    {
        return [Setting::class];
    }
}
