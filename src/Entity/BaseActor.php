<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="actor")
 */
abstract class BaseActor extends AbstractObject
{
    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $username;

    /**
     * @ORM\Column(type="text")
     */
    protected $summary;

    /**
     * @ORM\ManyToMany(targetEntity="BaseActor", mappedBy="following")
     */
    protected $followers;

    /**
     * @ORM\ManyToMany(targetEntity="BaseActor", inversedBy="followers")
     * @ORM\JoinTable(
     *     name="following",
     *     joinColumns={@ORM\JoinColumn(name="follower", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="following", referencedColumnName="id")}
     * )
     */
    protected $following;

    /**
     * @ORM\OneToMany(targetEntity="BaseActivity", mappedBy="actor")
     */
    protected $outboxActivities;

    /**
     * @ORM\ManyToMany(targetEntity="BaseActivity", mappedBy="receivingActors")
     */
    protected $inboxActivities;

    public function __construct()
    {
        $this->followers = new ArrayCollection();
        $this->following = new ArrayCollection();
        $this->outboxActivities = new ArrayCollection();
        $this->inboxActivities = new ArrayCollection();
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    public function getSummary()
    {
        return $this->summary;
    }

    public function setSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    public function getFollowers()
    {
        return $this->followers;
    }

    public function addFollower(BaseActor $actor)
    {
        if (!$this->followers->contains($actor)) {
            $actor->addFollowing($this);
            $this->followers[] = $actor;
        }
        return $this;
    }

    public function removeFollower(BaseActor $actor)
    {
        if ($this->followers->contains($actor)) {
            $actor->removeFollowing($this);
            $this->followers->removeElement($actor);
        }
        return $this;
    }

    public function getFollowing()
    {
        return $this->following;
    }

    public function addFollowing(BaseActor $actor)
    {
        if (!$this->following->contains($actor)) {
            $this->following[] = $actor;
        }
        return $this;
    }

    public function removeFollowing(BaseActor $actor)
    {
        if ($this->following->contains($actor)) {
            $this->following->removeElement($actor);
        }
        return $this;
    }

    public function getOutboxActivities()
    {
        return $this->outboxActivities;
    }

    public function addOutboxActivity(BaseActivity $activity)
    {
        if (!$this->outboxActivities->contains($activity)) {
            $this->outboxActivities[] = $activity;
        }
        return $this;
    }

    public function removeOutboxActivity(BaseActivity $activity)
    {
        if ($this->outboxActivities->contains($activity)) {
            $this->outboxActivities->removeElement($activity);
        }
        return $this;
    }

    public function getInboxActivities()
    {
        return $this->inboxActivities;
    }

    public function addInboxActivity(BaseActivity $activity)
    {
        if (!$this->inboxActivities->contains($activity)) {
            $this->inboxActivities[] = $activity;
        }
        return $this;
    }

    public function removeInboxActivity(BaseActivity $activity)
    {
        if ($this->inboxActivities->contains($activity)) {
            $this->inboxActivities->removeElement($activity);
        }
        return $this;
    }
}