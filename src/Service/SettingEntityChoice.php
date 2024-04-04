<?php

namespace OHMedia\SettingsBundle\Service;

use OHMedia\SettingsBundle\Entity\Setting;
use OHMedia\SecurityBundle\Service\EntityChoiceInterface;

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
