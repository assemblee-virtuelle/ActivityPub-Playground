<?php

namespace App\Entity;

use AV\ActivityPubBundle\Entity\Actor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="incoming_webhook")
 */
class IncomingWebhook implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private $apiKey;

    /**
     * @var Actor
     * @ORM\OneToOne(targetEntity="AV\ActivityPubBundle\Entity\Actor", cascade={"persist"})
     */
    private $actor;

    public function __construct(Actor $actor)
    {
        $this->actor = $actor;
    }

    public function getRoles()
    {
        return ['ROLE_USER'];
    }

    public function getPassword()
    {

    }

    public function getSalt()
    {

    }

    public function eraseCredentials()
    {

    }

    public function getApiKey()
    {
        return $this->apiKey;
    }

    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    public function getActor()
    {
        return $this->actor;
    }

    public function getUsername()
    {
        return $this->actor->getUsername();
    }
}