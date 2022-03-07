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

    public function set(string $id, $value)
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

        $string = array_key_exists($id, $this->transformers)
            ? $this->transformers[$id]->transform($value)
            : $value;

        $setting->setValue($string);

        $this->em->flush();

        $this->settings[$id] = $value;

        return $this;
    }

    public function get(string $id)
    {
        if ('' === $id) {
            return null;
        }

        if (!array_key_exists($id, $this->settings)) {
            $setting = $this->em->getRepository(Setting::class)->find($id);

            $string = $setting ? $setting->getValue() : null;

            $value = array_key_exists($id, $this->transformers)
                ? $this->transformers[$id]->reverseTransform($string)
                : $string;

            $this->settings[$id] = $value;
        }

        return $this->settings[$id];
    }

    public function addTransformer(TransformerInterface $transformer)
    {
        $this->transformers[$transformer->getId()] = $transformer;

        return $this;
    }
}
