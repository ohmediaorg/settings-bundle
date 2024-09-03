<?php

namespace OHMedia\SettingsBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use OHMedia\SettingsBundle\Entity\Setting;
use OHMedia\SettingsBundle\Interfaces\TransformerInterface;
use OHMedia\UtilityBundle\Service\EntityIdentifier;

class Settings
{
    private array $entities = [];
    private array $transformers = [];
    private array $values = [];
    private bool $init = false;

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

        $setting = $this->getEntity($id);

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

        $this->values[$id] = $value;

        return $this;
    }

    public function get(string $id): mixed
    {
        if ('' === $id) {
            return null;
        }

        if (!array_key_exists($id, $this->values)) {
            $setting = $this->getEntity($id);

            $string = $setting ? $setting->getValue() : null;

            if ($string && preg_match('/^ENTITY:/', $string)) {
                $parts = explode(':', $string);

                $value = $this->em->getRepository($parts[1])->find($parts[2]);
            } elseif (array_key_exists($id, $this->transformers)) {
                $value = $this->transformers[$id]->reverseTransform($string);
            } else {
                $value = $string;
            }

            $this->values[$id] = $value;
        }

        return $this->values[$id];
    }

    public function addTransformer(TransformerInterface $transformer): self
    {
        $this->transformers[$transformer->getId()] = $transformer;

        return $this;
    }

    private function getEntity(string $id): ?Setting
    {
        if (!$this->init) {
            $this->init = true;

            $entities = $this->em->getRepository(Setting::class)->findAll();

            foreach ($entities as $entity) {
                $this->entities[$entity->getId()] = $entity;
            }
        }

        if (!isset($this->entities[$id])) {
            $this->entities[$id] = $this->em->getRepository(Setting::class)->find($id);
        }

        return $this->entities[$id];
    }
}
