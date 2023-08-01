<?php

namespace OHMedia\SettingsBundle\Interfaces;

interface TransformerInterface
{
    public function getId(): string;

    public function transform($value): ?string;

    public function reverseTransform(?string $value);
}
