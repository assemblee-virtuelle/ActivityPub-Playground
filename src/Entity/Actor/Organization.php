<?php

namespace App\Entity\Actor;

use App\DbType\ActorType;
use App\Entity\BaseActor;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="organization")
 */
class Organization extends BaseActor
{
    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\BaseActor")
     * @ORM\JoinTable(
     *     name="authorization",
     *     joinColumns={@ORM\JoinColumn(name="controlled_actor_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="controlling_actor_id", referencedColumnName="id")}
     * )
     */
    protected $controllingActors;

    public function __construct()
    {
        $this->type = ActorType::ORGANIZATION;
        $this->controllingActors = new ArrayCollection();
        parent::__construct();
    }

    public function getControllingActors()
    {
        return $this->controllingActors;
    }

    public function addControllingActor(BaseActor $actor)
    {
        if (!$this->hasControllingActor($actor)) {
            $this->controllingActors[] = $actor;
        }
        return $this;
    }

    public function removeControllingActor(BaseActor $actor)
    {
        if ($this->hasControllingActor($actor)) {
            $this->controllingActors->removeElement($actor);
        }
        return $this;
    }

    public function hasControllingActor(BaseActor $actor)
    {
        return $this->controllingActors->contains($actor);
    }
}