<?php

namespace App\Service;

use App\Entity\ActivityPubObject;
use App\Entity\Field;
use Doctrine\ORM\EntityManagerInterface;

class ActivityPubService
{
    protected $em;

    protected $serverUrl;

    public function __construct(EntityManagerInterface $em, string $serverUrl)
    {
        $this->em = $em;
        $this->serverUrl = $serverUrl;
    }

    public function createActor(array $values) : bool
    {
        $actor = new ActivityPubObject();
        $values['id'] = $this->serverUrl . "/actor/" . $values['name'];

        foreach($values as $key => $value) {
            Field::withValue($actor, $key, $value);
        }
        $this->em->persist($actor);
        $this->em->flush();
    }

    public function postActivity(ActivityPubObject $actor, array $values) : bool
    {
        $activity = new ActivityPubObject();
        foreach($values as $key => $value) {
            Field::withValue($activity, $key, $value);
        }
        $this->em->persist($actor);
        $this->em->flush();
    }
}