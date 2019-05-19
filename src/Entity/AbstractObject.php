<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="base_object")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="discr", type="string")
 * @ORM\DiscriminatorMap({
 *     "Application" = "App\Entity\Actor\Application",
 *     "User" = "App\Entity\Actor\User",
 *     "Organization" = "App\Entity\Actor\Organization",
 *     "Activity" = "BaseActivity",
 *     "Object" = "BaseObject"
 * })
 */
abstract class AbstractObject
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(name="type", type="string")
     */
    protected $type;

    public function __toString()
    {
        return "Object " . $this->getType() . " #" . $this->getId();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }
}