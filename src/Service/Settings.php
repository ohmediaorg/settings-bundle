<?php

namespace OHMedia\SettingsBundle\Service;

use Doctrine\ORM\EntityManager;
use OHMedia\SettingsBundle\Entity\Setting;
use OHMedia\SettingsBundle\Interfaces\TransformerInterface;

class Settings
{
    private $em;
    private $transformers;
    private $settings;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $this->transformers = [];
        $this->settings = [];
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

        if ($this->isEntity($value)) {
            $string = implode(':', [
                'ENTITY',
                $value::class,
                $value->getId(),
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

    private function isEntity(mixed $value): bool
    {
        if (!is_object($value)) {
            return false;
        }

        $reflection = new \ReflectionClass($value::class);

        $attributes = $reflection->getAttributes();

        foreach ($attributes as $attribute) {
            if ('Doctrine\ORM\Mapping\Entity' === $attribute->getName()) {
                return true;
            }
        }

        return false;
    }
}
