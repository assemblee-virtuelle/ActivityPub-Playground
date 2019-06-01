<?php

namespace AV\ActivityPubBundle\Serializer;

use AV\ActivityPubBundle\Entity\Activity;
use AV\ActivityPubBundle\Entity\Actor;
use AV\ActivityPubBundle\Service\ActivityPubService;

class ActivitySerializer extends BaseSerializer
{
    /** @var string */
    private $flavour;

    /** @var ActivityPubService $activityPubService */
    private $activityPubService;

    public function __construct($flavour, ActivityPubService $activityPubService)
    {
        $this->ensureFlavour($flavour, [self::FLAVOUR_SMALL]);
        $this->flavour = $flavour;
        $this->activityPubService = $activityPubService;
    }

    /**
     * @param Activity $activity
     *
     * @return array
     */
    public function serialize($activity): ?array
    {
        $this->ensureType($activity, Activity::class);

        $activityUri = $this->activityPubService->getObjectUri($activity);

        $result = [
            "@context" => "https://www.w3.org/ns/activitystreams",
            "id" => $activityUri,
            "type" => $activity->getType(),
            "actor" => $activity->getActor() ? $this->activityPubService->getObjectUri($activity->getActor()) : null,
            "object" => $activity->getObject() ? $this->activityPubService->getObjectUri($activity->getObject()) : null
        ];

        return $result;
    }
}
