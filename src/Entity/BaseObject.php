<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="object")
 */
class BaseObject extends AbstractObject
{
    /**
     * @ORM\Column(type="text")
     */
    private $content;

    public function getContent()
    {
        return $this->content;
    }

    public function setContent(string $content)
    {
        $this->content = $content;
        return $this;
    }
}