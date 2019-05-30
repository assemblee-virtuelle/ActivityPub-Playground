<?php

namespace AV\ActivityPubBundle\Controller;

use AV\ActivityPubBundle\Entity\Actor;
use AV\ActivityPubBundle\Serializer\ActorSerializer;
use AV\ActivityPubBundle\Serializer\Serializable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ActorController extends BaseController
{
    /**
     * @Route("/actor/{username}", name="actor_profile", methods={"GET"})
     */
    public function actorProfile(string $username)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var ActorSerializer $actorSerializer */
        $actorSerializer = $this->container->get('activity_pub.serializer.actor.full');

        /** @var Actor $actor */
        $actor = $em->getRepository(Actor::class)->findOneBy(['username' => $username]);
        if( !$actor ) throw new NotFoundHttpException();

        return $this->json(new Serializable($actor, $actorSerializer));
    }
}