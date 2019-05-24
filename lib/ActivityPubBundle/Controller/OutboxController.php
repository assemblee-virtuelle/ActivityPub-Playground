<?php

namespace AV\ActivityPubBundle\Controller;

use AV\ActivityPubBundle\Entity\Activity;
use AV\ActivityPubBundle\Entity\Actor;
use AV\ActivityPubBundle\Service\ActivityPubService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class OutboxController extends BaseController
{
    /**
     * @Route("/actor/{username}/outbox", name="actor_outbox", methods={"POST"})
     */
    public function postActivity(string $username, Request $request)
    {
        /** @var ActivityPubService $activityPubService */
        $activityPubService = $this->container->get('activity_pub.service');

        /** @var Actor $user */
        $user = $this->getUser()->getActor();
        $json = $this->parseBodyAsJson($request);

        if( $user->getUsername() !== $username ) {
            throw new AccessDeniedHttpException("You are not allowed to post to someone else's outbox");
        }

        if( !$json ) {
            throw new BadRequestHttpException("You must post a JSON object to this endpoint");
        }

        $activity = $activityPubService->handleActivity($json, $user);

        return new Response(null, Response::HTTP_CREATED, ['Location' => $activityPubService->getObjectUri($activity)]);
    }

    /**
     * @Route("/actor/{username}/outbox", name="actor_outbox", methods={"GET"})
     */
    public function readOutbox(string $username)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var ActivityPubService $activityPubService */
        $activityPubService = $this->container->get('activity_pub.service');

        /** @var Actor $user */
        $user = $this->getUser()->getActor();

        /** @var Actor $actor */
        $actor = $em->getRepository(Actor::class)->findOneBy(['username' => $username]);
        if( !$actor ) throw new NotFoundHttpException();

        $actorUri = $activityPubService->getObjectUri($actor);

        $activities = $actor
            ->getOutboxActivities()
            ->filter(function (Activity $activity) use ( $user ) {
                return $activity->getIsPublic() || $activity->getReceivingActors()->contains($user);
            })
            ->map(function (Activity $activity) use ( $actorUri, $activityPubService ) {
                return([
                    "type" => $activity->getType(),
                    "actor" => $actorUri,
                    "object" => $activity->getObject() ? $activityPubService->getObjectUri($activity->getObject()) : null
                ]);
            });

        return new JsonResponse([
            "@context" => "https://www.w3.org/ns/activitystreams",
            "id" => $actorUri . "/outbox",
            "type" => "OrderedCollection",
            "totalItems" => count($activities),
            "orderedItems" => $activities->toArray()
        ]);
    }
}