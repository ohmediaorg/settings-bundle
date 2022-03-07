<?php

namespace JstnThms\SettingsBundle\Settings;

interface SettingsTransformerInterface
{
    public function getId() : string;
    public function transform($value) : ?string;
    public function reverseTransform(?string $value);
}
