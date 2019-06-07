<?php

namespace AV\ActivityPubBundle\Service;

use AV\ActivityPubBundle\DbType\ActorType;
use AV\ActivityPubBundle\DbType\ObjectType;
use AV\ActivityPubBundle\DbType\ActivityType;
use AV\ActivityPubBundle\Entity\Activity;
use AV\ActivityPubBundle\Entity\Actor;
use AV\ActivityPubBundle\Entity\BaseObject;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ActivityPubService
{
    protected $em;

    protected $authorizationChecker;

    protected $serverBaseUrl;

    protected $parser;

    public const PUBLIC_POST_URI = 'https://www.w3.org/ns/activitystreams#Public';

    public function __construct(string $serverBaseUrl, EntityManagerInterface $em, AuthorizationCheckerInterface $authorizationChecker, ActivityStreamsParser $parser)
    {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->serverBaseUrl = $serverBaseUrl;
        $this->parser = $parser;
    }

    public function handleActivity(array $json, Actor $loggedActor) : Activity
    {
        if( $json['@context'] !== 'https://www.w3.org/ns/activitystreams' ) {
            throw new BadRequestHttpException("Only ActivityStreams objects are allowed");
        }

        // If an object or actor is passed directly, wrap it inside a Create activity
        if( ObjectType::includes($json['type']) || ActorType::includes($json['type']) ) {
            $json = [
                'type' => ActivityType::CREATE,
                'to' => isset($json['to']) ? $json['to'] : null,
                'actor' => $json['attributedTo'],
                'object' => $json
            ];
        }

        $activityType = $json['type'];

        /** @var Activity $activity */
        $activity = $this->parser->parse($json);

        if( array_key_exists('actor', $json) ) {
            $activity->setActor($this->getActorFromUri($json['actor']));

            // Make sure the logged actor has the right to post as the posting actor
            if( !$this->authorizationChecker->isGranted($activityType, $activity) ) {
                throw new UnauthorizedHttpException("You cannot post as {$activity->getActor()->getUsername()}");
            }
        } else {
            $activity->setActor($loggedActor);
        }

        //////////////////
        // SIDE EFFECTS
        //////////////////

        switch($activityType)
        {
            case ActivityType::CREATE:
                $this->handleCreate($activity, $json['object']);
                break;

            case ActivityType::FOLLOW:
                $this->handleFollow($activity, $json['object']);
                break;

            default:
                throw new BadRequestHttpException("Unhandled activity : $activityType");
        }

        // TODO: send an ActivityEvent

        $this->em->persist($activity);
        $this->em->flush();

        return $activity;
    }

    protected function handleCreate(Activity $activity, array $objectJson)
    {
        if ( ActorType::includes($activity->getType()) ) {
            if( !in_array($activity->getType(), Actor::CONTROLLABLE_ACTORS) )
                throw new BadRequestHttpException("This type of actor cannot be created");
            $activity->getObject()->addControllingActor($activity->getActor());
        }

        $activity->setIsPublic(true);

        // Forward activity
        if( isset($objectJson['to']) ) {
            foreach( $objectJson['to'] as $actorUri ) {
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
        }
    }

    protected function handleFollow(Activity $activity, string $objectJson)
    {
        $actorToFollow = $this->getActorFromUri($objectJson);
        $actorToFollow->addFollower($activity->getActor());
        $activity->setObject($activity->getActor());
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
            case 'AV\ActivityPubBundle\Entity\Activity':
                return $this->serverBaseUrl . '/activity/' . $object->getId();
                break;

            case 'AV\ActivityPubBundle\Entity\BaseObject':
                return $this->serverBaseUrl . '/object/' . $object->getId();
                break;

            case 'AV\ActivityPubBundle\Entity\Actor':
                return $this->serverBaseUrl . '/actor/' . $object->getUsername();
                break;

            default:
                throw new BadRequestHttpException("Unknown object : " . ClassUtils::getClass($object) );
        }
    }
}