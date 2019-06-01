<?php

namespace AV\ActivityPubBundle\Serializer;

use AV\ActivityPubBundle\Entity\Actor;
use AV\ActivityPubBundle\Service\ActivityPubService;

class ActorSerializer extends BaseSerializer
{
    /** @var string */
    private $flavour;

    /** @var ActivityPubService $activityPubService */
    private $activityPubService;

    public function __construct($flavour, ActivityPubService $activityPubService)
    {
        $this->ensureFlavour($flavour, [self::FLAVOUR_MEDIUM, self::FLAVOUR_FULL]);
        $this->flavour = $flavour;
        $this->activityPubService = $activityPubService;
    }

    /**
     * @param Actor $actor
     *
     * @return array
     */
    protected function getDataToSerialize($actor): ?array
    {
        $this->ensureType($actor, Actor::class);

        $actorUri = $this->activityPubService->getObjectUri($actor);

        $result = [
            "@context" => "https://www.w3.org/ns/activitystreams",
            "id" => $actorUri,
            "type" => $actor->getType(),
            "preferredUsername" => $actor->getUsername(),
            "summary" => $actor->getSummary()
        ];

        if ($this->flavour === self::FLAVOUR_FULL) {
            $result = array_merge($result, [
                "@context" => [
                    "https://www.w3.org/ns/activitystreams",
                    "https://w3id.org/security/v1"
                ],
                "inbox" => $actorUri . '/inbox',
                "outbox" => $actorUri . '/outbox',
                "followers" => $actorUri . '/followers',
                "following" => $actorUri . '/following',
                "publicKey" => [
                    "id" => $actorUri . "#main-key",
                    "owner" => $actorUri,
                    "publicKeyPem" => "-----BEGIN PUBLIC KEY-----...-----END PUBLIC KEY-----"
                ]
            ]);
        }

        return $result;
    }
}
