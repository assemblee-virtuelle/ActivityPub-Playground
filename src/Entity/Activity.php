<?php

namespace App\Entity;

use App\DbType\ActivityType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="activity")
 */
class Activity
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @see ActivityType
     *
     * @var string
     * @ORM\Column(name="type", type="activity_type")
     */
    private $type;

    /**
     * @ORM\ManyToOne(targetEntity="Actor", inversedBy="outboxActivities")
     */
    private $actor;

    /**
     * Many Activities are posted to many Actors's inboxes
     * @ORM\ManyToMany(targetEntity="Actor", inversedBy="inboxActivities")
     * @ORM\JoinTable(name="activity_receiving_actor")
     */
    private $receivingActors;

    /**
     * @ORM\Column(name="is_public", type="boolean")
     */
    private $isPublic;

    /**
     * Each Activity has one or zero Object
     * @ORM\OneToOne(targetEntity="ActivityObject", inversedBy="activity", cascade={"persist"})
     */
    private $object;

    public function __construct()
    {
        $this->isPublic = false;
        $this->receivingActors = new ArrayCollection();
    }

    public function __toString()
    {
        return "Activity " . $this->getType() . " #" . $this->getId();
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): Activity
    {
        $this->type = $type;
        return $this;
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

    public function addReceivingActor(Actor $actor)
    {
        if (!$this->receivingActors->contains($actor)) {
            $actor->addInboxActivity($this);
            $this->receivingActors[] = $actor;
        }
        return $this;
    }

    public function removeActorInbox(Actor $actor)
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