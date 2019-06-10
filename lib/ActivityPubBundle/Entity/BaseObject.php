<?php

namespace AV\ActivityPubBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="AV\ActivityPubBundle\Repository\ObjectRepository")
 * @ORM\Table(name="object")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="class_name", type="string")
 * @ORM\DiscriminatorMap({
 *     "Actor" = "Actor",
 *     "Activity" = "Activity",
 *     "Object" = "BaseObject",
 *     "Place" = "Place"
 * })
 */
class BaseObject
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     */
    protected $type;

    /**
     * Each Activity has one or zero Object
     * @ORM\OneToOne(targetEntity="Activity", mappedBy="object")
     */
    private $createActivity;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $summary;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $image;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $published;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $updated;

    /**
     * @ORM\OneToOne(targetEntity="BaseObject", cascade={"persist", "remove"})
     */
    protected $location;

    /**
     * @ORM\ManyToMany(targetEntity="BaseObject", cascade={"persist"})
     * @ORM\JoinTable(
     *     name="tag",
     *     joinColumns={@ORM\JoinColumn(name="tagged_object_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="tag_id", referencedColumnName="id")}
     * )
     */
    protected $tags;

    /**
     * @ORM\OneToOne(targetEntity="BaseObject", cascade={"persist", "remove"})
     */
    protected $attachment;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function __toString() : string
    {
        return "Object " . $this->getType() . " #" . $this->getId();
    }

    public function set(string $name, $value)
    {
        $this->{$name} = $value;

        return $this;
    }

    public function getId() : ?int
    {
        return $this->id;
    }

    public function setId(int $id) : self
    {
        $this->id = $id;
        return $this;
    }

    public function getType() : string
    {
        return $this->type;
    }

    public function setType(string $type) : self
    {
        $this->type = $type;
        return $this;
    }

    public function getCreateActivity() : Activity
    {
        return $this->createActivity;
    }

    public function getName() : ?string
    {
        return $this->name;
    }

    public function setName(?string $name) : self
    {
        $this->name = $name;
        return $this;
    }

    public function getSummary() : ?string
    {
        return $this->summary;
    }

    public function setSummary(?string $summary) : self
    {
        $this->summary = $summary;
        return $this;
    }

    public function getContent() : ?string
    {
        return $this->content;
    }

    public function setContent(?string $content) : self
    {
        $this->content = $content;
        return $this;
    }

    public function getImage() : ?string
    {
        return $this->image;
    }

    public function setImage(?string $image) : self
    {
        $this->image = $image;
        return $this;
    }

    public function getUrl() : ?string
    {
        return $this->url;
    }

    public function setUrl(?string $url) : self
    {
        $this->url = $url;
        return $this;
    }

    public function getPublished() : ?\DateTime
    {
        return $this->published;
    }

    public function setPublished(?\DateTime $published) : self
    {
        $this->published = $published;
        return $this;
    }

    public function getUpdated() : ?\DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?\DateTime $updated) : self
    {
        $this->updated = $updated;
        return $this;
    }

    public function getLocation() : ?BaseObject
    {
        return $this->location;
    }

    public function setLocation(?BaseObject $location) : self
    {
        $this->location = $location;
        return $this;
    }

    public function getTags() : Collection
    {
        return $this->tags;
    }

    public function addTag(BaseObject $tag) : self
    {
        if (!$this->hasTag($tag)) {
            $this->tags[] = $tag;
        }
        return $this;
    }

    public function removeTag(BaseObject $tag) : self
    {
        if ($this->hasTag($tag)) {
            $this->tags->removeElement($tag);
        }
        return $this;
    }

    public function hasTag(BaseObject $tag) : bool
    {
        return $this->tags->contains($tag);
    }

    public function getAttachment() : ?BaseObject
    {
        return $this->attachment;
    }

    public function setAttachment(?BaseObject $attachment) : self
    {
        $this->attachment = $attachment;
        return $this;
    }
}