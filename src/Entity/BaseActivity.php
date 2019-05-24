<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="activity")
 */
class BaseActivity extends BaseObject
{
    /**
     * @ORM\ManyToOne(targetEntity="BaseActor", inversedBy="outboxActivities")
     */
    private $actor;

    /**
     * Many Activities are posted to many Actors's inboxes
     * @ORM\ManyToMany(targetEntity="BaseActor", inversedBy="inboxActivities")
     * @ORM\JoinTable(name="activity_receiving_actor")
     */
    private $receivingActors;

    /**
     * @ORM\Column(name="is_public", type="boolean")
     */
    private $isPublic;

    /**
     * Each Activity has one or zero Object
     * @ORM\OneToOne(targetEntity="BaseObject", cascade={"persist"})
     */
    private $object;

    public function __construct()
    {
        $this->isPublic = false;
        $this->receivingActors = new ArrayCollection();
    }

    public function getActor()
    {
        return $this->actor;
    }

    public function setActor($actor)
    {
        $this->actor = $actor;
        return $this;
    }

    public function getReceivingActors()
    {
        return $this->receivingActors;
    }

    public function addReceivingActor(BaseActor $actor)
    {
        if (!$this->receivingActors->contains($actor)) {
            $actor->addInboxActivity($this);
            $this->receivingActors[] = $actor;
        }
        return $this;
    }

    public function removeActorInbox(BaseActor $actor)
    {
        if ($this->receivingActors->contains($actor)) {
            $this->receivingActors->removeElement($actor);
        }
        return $this;
    }

    public function getIsPublic()
    {
        return $this->isPublic;
    }

    public function setIsPublic($isPublic)
    {
        $this->isPublic = $isPublic;
        return $this;
    }

    public function getObject()
    {
        return $this->object;
    }

    public function setObject($object)
    {
        $this->object = $object;
        return $this;
    }
}