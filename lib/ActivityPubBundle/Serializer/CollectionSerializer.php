<?php

namespace AV\ActivityPubBundle\Serializer;

use AV\ActivityPubBundle\Entity\Activity;
use AV\ActivityPubBundle\Entity\BaseObject;
use AV\ActivityPubBundle\Entity\OrderedCollection;
use AV\ActivityPubBundle\Service\ActivityPubService;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class CollectionSerializer extends BaseSerializer
{
    /** @var ActivityPubService $activityPubService */
    private $activityPubService;

    /** @var ActivitySerializer $activitySerializer */
    private $activitySerializer;

    public function __construct(ActivityPubService $activityPubService, ActivitySerializer $activitySerializer)
    {
        $this->activityPubService = $activityPubService;
        $this->activitySerializer = $activitySerializer;
    }

    /**
     * @param OrderedCollection $collection
     *
     * @return array
     */
    protected function getDataToSerialize($collection): ?array
    {
        $result = [
            "@context" => "https://www.w3.org/ns/activitystreams",
            "id" => $collection->getId(),
            "type" => "OrderedCollection",
            "totalItems" => count($collection->getObjects()),
            "orderedItems" => $collection->getObjects()->map(function (BaseObject $object) {
                if( is_a($object, Activity::class) ) {
                    return( $this->activitySerializer->serialize($object) );
                } else {
                    throw new BadRequestHttpException("Cannot serialize object of type" . get_class($object));
                }
            })
        ];

        return $result;
    }
}
