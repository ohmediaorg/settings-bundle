<?php

namespace OHMedia\SettingsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Setting
{
    #[ORM\Id()]
    #[ORM\Column(type: 'string', length: 255)]
    private $id;

    #[ORM\Column(type: 'text', nullable: true)]
    private $value;

    public function getId()
    {
        return $this->id;
    }

    public function setId(string $id)
    {
        $this->id = $id;

        return $this;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
