<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\ActivityPubService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;

class OutboxController extends BaseController
{
    /**
     * @Route("/actor/{username}/outbox", name="actor_outbox", methods={"POST"})
     */
    public function postActivity(string $username, Request $request, ActivityPubService $activityPubService)
    {
        /** @var User $user */
        $user = $this->getUser();
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
}