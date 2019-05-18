<?php

namespace App\Service;

use App\DbType\ActivityObjectType;
use App\DbType\ActivityType;
use App\Entity\Activity;
use App\Entity\ActivityObject;
use App\Entity\Actor;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ActivityPubService
{
    protected $em;

    protected $serverBaseUrl;

    public const PUBLIC_POST_URI = 'https://www.w3.org/ns/activitystreams#Public';

    public function __construct(EntityManagerInterface $em, string $serverBaseUrl)
    {
        $this->em = $em;
        $this->serverBaseUrl = $serverBaseUrl;
    }

    public function handleActivity(array $json, Actor $actor) : Activity
    {
        if( $json['@context'] !== 'https://www.w3.org/ns/activitystreams' ) {
            throw new BadRequestHttpException("Only ActivityStreams objects are allowed");
        }

        // If an object is passed directly, wrap it inside a Create activity
        if( in_array($json['type'], ActivityObjectType::getValues()) ) {
            $json = [
                'type' => ActivityType::CREATE,
                'to' => $json['to'],
                'actor' => $json['attributedTo'],
                'object' => $json
            ];
        }

        $activityType = $json['type'];

        if( !in_array($activityType, ActivityType::getValues()) ) {
            throw new BadRequestHttpException("Unknown activity type : $activityType");
        }

        // VALIDATION
        // Check that the actor and attributedTo field are the ID of the logged user

        $activity = new Activity();
        $activity
            ->setType($activityType)
            ->setActor($actor);

        //////////////////
        // SIDE EFFECTS
        //////////////////

        switch($activityType)
        {
            case ActivityType::CREATE: {
                // Create object
                $object = new ActivityObject();
                $object
                    ->setType($json['object']['type'])
                    ->setContent($json['object']['content']);
                $activity->setObject($object);

                // Forward activity
                foreach( $json['object']['to'] as $actorUri ) {
                    if( $actorUri === ActivityPubService::PUBLIC_POST_URI ) {
                        $activity->setIsPublic(true);
                    } elseif ( $followers = $this->getFollowersFromUri($actorUri) ) {
                        foreach( $followers as $follower ) {
                            $activity->addReceivingActor($follower);
                        }
                    } elseif ( $actor = $this->getActorFromUri($actorUri) ) {
                        $activity->addReceivingActor($actor);
                    } else {
                        throw new BadRequestHttpException("Unknown actor URI : $actorUri");
                    }
                }

                break;
            }

            case ActivityType::FOLLOW: {
                $actorToFollow = $this->getActorFromUri($json['object']);
                $actorToFollow->addFollower($actor);
                break;
            }

            default: {
                throw new BadRequestHttpException("Unhandled activity : $activityType");
            }
        }

        // TODO: notify followers

        $this->em->persist($actor);
        $this->em->persist($activity);
        $this->em->flush();

        return $activity;
    }

    public function getActorFromUri(string $uri) : Actor
    {
        preg_match('/\/actor\/(\w*)\//', $uri, $matches );
        if( !$matches ) return null;
        /** @var Actor $actor */
        $actor = $this->em->getRepository(Actor::class)
            ->findOneBy(['username' => $matches[1]]);
        return $actor;
    }

    public function getFollowersFromUri(string $uri) : Collection
    {
        preg_match('/\/actor\/(\w*)\/followers/', $uri, $matches );
        if( !$matches ) return null;
        /** @var Actor $actor */
        $actor = $this->em->getRepository(Actor::class)
            ->findOneBy(['username' => $matches[1]]);
        return $actor->getFollowers();
    }

    public function getObjectUri($object) {
        switch( ClassUtils::getClass($object) ) {
            case 'App\Entity\Activity':
                return $this->serverBaseUrl . '/activity/' . $object->getId();
                break;

            case 'App\Entity\ActivityObject':
                return $this->serverBaseUrl . '/object/' . strtolower($object->getType()) . '/' . $object->getId();
                break;

            case 'App\Entity\Actor':
            case 'App\Entity\Application':
            case 'App\Entity\User':
                return $this->serverBaseUrl . '/actor/' . $object->getUsername();
                break;

            default:
                throw new BadRequestHttpException("Unknown object : " . ClassUtils::getClass($object) );
        }
    }
}