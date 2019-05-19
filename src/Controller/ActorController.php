<?php

namespace App\Controller;

use App\Entity\BaseActor;
use App\Service\ActivityPubService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

class ActorController extends BaseController
{
    /**
     * @Route("/actor/{username}", name="actor_profile", methods={"GET"})
     */
    public function actorProfile(string $username, ActivityPubService $activityPubService)
    {
        $em = $this->getDoctrine()->getManager();

        /** @var BaseActor $actor */
        $actor = $em->getRepository(BaseActor::class)->findOneBy(['username' => $username]);

        if( !$actor ) throw new NotFoundHttpException();

        $actorUri = $activityPubService->getObjectUri($actor);

        $json = [
            "@context" => [
                "https://www.w3.org/ns/activitystreams",
                "https://w3id.org/security/v1"
            ],
            "id" => $actorUri,
            "type" => $actor->getType(),
            "preferredUsername" => $actor->getUsername(),
            "summary" => $actor->getSummary(),

            "inbox" => $actorUri . '/inbox',
            "outbox" => $actorUri . '/outbox',
            "followers" => $actorUri . '/followers',
            "following" => $actorUri . '/following',

            "publicKey" => [
                "id" => $actorUri . "#main-key",
                "owner" => $actorUri,
                "publicKeyPem" => "-----BEGIN PUBLIC KEY-----...-----END PUBLIC KEY-----"
            ]
        ];

        return new JsonResponse($json);
    }
}