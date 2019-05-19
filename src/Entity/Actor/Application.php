<?php

namespace App\Entity\Actor;

use App\DbType\ActorType;
use App\Entity\BaseActor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity
 * @ORM\Table(name="application")
 */
class Application extends BaseActor implements UserInterface
{
    /**
     * @ORM\Column(type="string", length=16, unique=true)
     */
    private $apiKey;

    public function __construct()
    {
        $this->type = ActorType::APPLICATION;
        parent::__construct();
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
}