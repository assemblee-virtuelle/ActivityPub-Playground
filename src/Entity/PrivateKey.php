<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * The keys table holds the private keys associated with ActivityPub actors
 *
 * @ORM\Entity @Table(name="keys")
 */
class PrivateKey
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $id;

    /**
     * The private key
     *
     * @var string
     * @ORM\Column(type="string")
     */
    protected $key;

    /**
     * The object associated with this private key
     *
     * @var ActivityPubObject
     * @ORM\OneToOne(targetEntity="ActivityPubObject", inversedBy="privateKey")
     */
    protected $object;

    /**
     * Creates a new PrivateKey
     *
     * Don't call this directly - instead, use ActivityPubObject->setPrivateKey()
     * @param string $key The private key as a string
     * @param ActivityPubObject $object The object associated with this key
     */
    public function __construct( $key, ActivityPubObject $object )
    {
        $this->key = $key;
        $this->object = $object;
    }

    /**
     * Sets the private key string
     *
     * Don't call this directly - instead, use ActivityPubObject->setPrivateKey()
     * @param string $key The private key as a string
     */
    public function setKey( $key )
    {
        $this->key = $key;
    }
}
