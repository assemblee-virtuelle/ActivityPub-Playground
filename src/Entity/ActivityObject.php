<?php

namespace App\Entity;

use App\DbType\ActivityObjectType;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="activity_object")
 */
class ActivityObject
{
    /**
     * @ORM\Id
     * @ORM\Column(name="id", type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @see ActivityObjectType
     *
     * @var string
     * @ORM\Column(name="type", type="activity_object_type")
     */
    private $type;

    /**
     * @ORM\Column(type="text")
     */
    private $content;

    /**
     * @ORM\OneToOne(targetEntity="Activity", mappedBy="object")
     */
    private $activity;

    public function __toString()
    {
        return "ActivityObject " . $this->getType() . " #" . $this->getId();
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

    public function setType(string $type)
    {
        $this->type = $type;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }

    public function getActivity()
    {
        return $this->activity;
    }

    public function setActivity(Activity $activity)
    {
        $this->activity = $activity;
        return $this;
    }
}