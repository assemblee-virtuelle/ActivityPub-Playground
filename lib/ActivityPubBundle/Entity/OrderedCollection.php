<?php

namespace AV\ActivityPubBundle\Entity;

use Doctrine\Common\Collections\Collection;

class OrderedCollection
{
    /**
     * @var string $id
     */
    protected $id;

    /**
     * @var BaseObject[] $objects
     */
    protected $objects;

    public function __construct(string $id, Collection $objects)
    {
        $this->id = $id;
        $this->objects = $objects;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getObjects()
    {
        return $this->objects;
    }
}