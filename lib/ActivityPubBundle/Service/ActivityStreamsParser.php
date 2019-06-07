<?php

namespace AV\ActivityPubBundle\Service;

use AV\ActivityPubBundle\DbType\ActivityType;
use AV\ActivityPubBundle\DbType\ActorType;
use AV\ActivityPubBundle\DbType\ObjectType;
use AV\ActivityPubBundle\Entity\Activity;
use AV\ActivityPubBundle\Entity\Actor;
use AV\ActivityPubBundle\Entity\BaseObject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ActivityStreamsParser
{
    protected $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function parse(array $json) : BaseObject
    {
        if( ActivityType::includes($json['type']) ) {
            $activity = new Activity();
            $this->parseActivity($activity, $json);
            return $activity;
        } elseif ( ObjectType::includes($json['type']) ) {
            $object = new BaseObject();
            $this->parseObject($object, $json);
            return $object;
        } elseif ( ActorType::includes($json['type']) ) {
            $actor = new Actor();
            $this->parseActor($actor, $json);
            return $actor;
        } else {
            throw new BadRequestHttpException("Unhandled object : {$json['type']}");
        }
    }

    protected function parseActivity(Activity $activity, array $json)
    {
        $this->parseObject($activity, $json);

        if( array_key_exists('object', $json) ) {
            $object = new BaseObject();
            $this->parseObject($object, $json['object']);
            $activity->setObject($object);
        }
    }

    protected function parseObject(BaseObject $object, array $json)
    {
        $this->parseScalarValues($object, $json);

        if( array_key_exists('location', $json) ) {
            $location = new BaseObject();
            $this->parseObject($location, $json['location']);
            $object->setLocation($location);
        }
    }

    protected function parseActor(Actor $actor, array $json)
    {
        $this->parseObject($actor, $json);
    }

    protected function parseScalarValues(BaseObject $object, $json)
    {
        foreach( $this->getFieldTypes(get_class($object)) as $fieldName => $fieldType) {
            if( !array_key_exists($fieldName, $json) ) continue;

            switch($fieldType) {
                case "string":
                case "text":
                    $object->set($fieldName, $json[$fieldName]);
                    break;

                case "datetime":
                    $object->set($fieldName, new \DateTime($json[$fieldName]));
                    break;

                default:
                    // Do nothing
            }
        }
    }

    protected function getFieldTypes($className)
    {
        $fieldTypes = [];
        $metadata = $this->em->getMetadataFactory()->getMetadataFor($className);


        foreach( $metadata->getFieldNames() as $fieldName ) {
            $fieldTypes[$fieldName] = $metadata->getTypeOfField($fieldName);
        };

        return $fieldTypes;
    }
}