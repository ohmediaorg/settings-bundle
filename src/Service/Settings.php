<?php

namespace OHMedia\SettingsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OHMedia\SettingsBundle\Entity\Setting;
use OHMedia\SettingsBundle\Interfaces\TransformerInterface;
use OHMedia\UtilityBundle\Service\EntityIdentifier;

class Settings
{
    private array $settings = [];
    private array $transformers = [];

    public function __construct(
        private EntityIdentifier $entityIdentifier,
        private EntityManagerInterface $em
    ) {
    }

    public function set(string $id, $value): self
    {
        if ('' === $id) {
            return $this;
        }

        $setting = $this->em->getRepository(Setting::class)->find($id);

        if (!$setting) {
            $setting = new Setting();
            $setting->setId($id);

            $this->em->persist($setting);
        }

        if ($entityId = $this->entityIdentifier->get($value)) {
            $string = implode(':', [
                'ENTITY',
                $value::class,
                $entityId,
            ]);
        } elseif (array_key_exists($id, $this->transformers)) {
            $string = $this->transformers[$id]->transform($value);
        } else {
            $string = $value;
        }

        $setting->setValue($string);

        $this->em->flush();

        $this->settings[$id] = $value;

        return $this;
    }

    public function get(string $id): mixed
    {
        if ('' === $id) {
            return null;
        }

        if (!array_key_exists($id, $this->settings)) {
            $setting = $this->em->getRepository(Setting::class)->find($id);

            $string = $setting ? $setting->getValue() : null;

            if ($string && preg_match('/^ENTITY:/', $string)) {
                $parts = explode(':', $string);

                $value = $this->em->getRepository($parts[1])->find($parts[2]);
            } elseif (array_key_exists($id, $this->transformers)) {
                $value = $this->transformers[$id]->reverseTransform($string);
            } else {
                $value = $string;
            }

            $this->settings[$id] = $value;
        }

        return $this->settings[$id];
    }

    public function addTransformer(TransformerInterface $transformer): self
    {
        $this->transformers[$transformer->getId()] = $transformer;

        return $this;
    }
}
