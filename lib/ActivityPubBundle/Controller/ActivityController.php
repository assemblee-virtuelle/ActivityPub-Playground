<?php

namespace AV\ActivityPubBundle\Controller;

use AV\ActivityPubBundle\Entity\Activity;
use AV\ActivityPubBundle\Entity\Actor;
use AV\ActivityPubBundle\Serializer\ObjectSerializer;
use AV\ActivityPubBundle\Serializer\Serializable;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ActivityController extends BaseController
{
    /**
     * @Route("/activity/{id}", name="activity_read", methods={"GET"})
     */
    public function readActivity(int $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var ObjectSerializer $objectSerializer */
        $activitySerializer = $this->container->get('activity_pub.serializer.activity.medium');

        /** @var Actor $actor */
        $activity = $em->getRepository(Activity::class)->findOneBy(['id' => $id]);
        if( !$activity ) throw new NotFoundHttpException();

        return $this->json(new Serializable($activity, $activitySerializer));
    }
}