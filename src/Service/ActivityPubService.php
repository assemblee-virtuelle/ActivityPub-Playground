<?php

namespace App\Service;

use App\DbType\ActorType;
use App\DbType\ObjectType;
use App\DbType\ActivityType;
use App\Entity\BaseActivity;
use App\Entity\BaseActor;
use App\Entity\BaseObject;
use App\Entity\Actor\Organization;
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

    public const PUBLIC_POST_URI = 'https://www.w3.org/ns/activitystreams#Public';

    public function __construct(EntityManagerInterface $em, AuthorizationCheckerInterface $authorizationChecker, string $serverBaseUrl)
    {
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->serverBaseUrl = $serverBaseUrl;
    }

    public function handleActivity(array $json, BaseActor $actor) : BaseActivity
    {
        if( $json['@context'] !== 'https://www.w3.org/ns/activitystreams' ) {
            throw new BadRequestHttpException("Only ActivityStreams objects are allowed");
        }

        // If an object or actor is passed directly, wrap it inside a Create activity
        if( in_array($json['type'], ObjectType::getValues()) || in_array($json['type'], ActorType::getValues()) ) {
            $json = [
                'type' => ActivityType::CREATE,
                'to' => isset($json['to']) ? $json['to'] : null,
                'actor' => $json['attributedTo'],
                'object' => $json
            ];
        }

        $activityType = $json['type'];

        if( !in_array($activityType, ActivityType::getValues()) ) {
            throw new BadRequestHttpException("Unknown activity type : $activityType");
        }

        $actor = $this->getActorFromUri($json['actor']);

        $activity = new BaseActivity();
        $activity
            ->setType($activityType)
            ->setActor($actor);

        // Make sure the logged user has the right to post as the posting actor
        if( !$this->authorizationChecker->isGranted($activityType, $activity) ) {
            throw new UnauthorizedHttpException("You cannot post as {$actor->getUsername()}");
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

        // TODO: notify followers

        $this->em->persist($actor);
        $this->em->persist($activity);
        $this->em->flush();

        return $activity;
    }

    protected function handleCreate(BaseActivity $activity, array $objectJson)
    {
        if( in_array($objectJson['type'], ObjectType::getValues()) ) {
            $object = new BaseObject();
            $object
                ->setType($objectJson['type'])
                ->setContent($objectJson['content']);
        } elseif ( in_array($objectJson['type'], ActorType::getValues()) ) {
            if( !in_array($objectJson['type'], BaseActor::CONTROLLABLE_ACTORS) )
                throw new BadRequestHttpException("This type of actor cannot be created");

            $object = new BaseActor();
            $object
                ->setType($objectJson['type'])
                ->setUsername($objectJson['username'])
                ->setName($objectJson['name'])
                ->addControllingActor($activity->getActor());
        } else {
            throw new BadRequestHttpException("Unhandled object : {$objectJson['type']}");
        }

        $activity->setObject($object);

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

    protected function handleFollow(BaseActivity $activity, string $objectJson)
    {
        $actorToFollow = $this->getActorFromUri($objectJson);
        $actorToFollow->addFollower($activity->getActor());
        $activity->setObject($activity->getActor());
    }

    public function getActorFromUri(string $uri) : BaseActor
    {
        preg_match('/\/actor\/(\w*)\//', $uri, $matches );
        if( !$matches ) return null;
        /** @var BaseActor $actor */
        $actor = $this->em->getRepository(BaseActor::class)
            ->findOneBy(['username' => $matches[1]]);
        return $actor;
    }

    public function getFollowersFromUri(string $uri) : Collection
    {
        preg_match('/\/actor\/(\w*)\/followers/', $uri, $matches );
        if( !$matches ) return null;
        /** @var BaseActor $actor */
        $actor = $this->em->getRepository(BaseActor::class)
            ->findOneBy(['username' => $matches[1]]);
        return $actor->getFollowers();
    }

    public function getObjectUri($object) {
        switch( ClassUtils::getClass($object) ) {
            case 'App\Entity\BaseActivity':
                return $this->serverBaseUrl . '/activity/' . $object->getId();
                break;

            case 'App\Entity\BaseObject':
                return $this->serverBaseUrl . '/object/' . strtolower($object->getType()) . '/' . $object->getId();
                break;

            case 'App\Entity\Actor\Organization':
            case 'App\Entity\Actor\Application':
            case 'App\Entity\Actor\User':
                return $this->serverBaseUrl . '/actor/' . $object->getUsername();
                break;

            default:
                throw new BadRequestHttpException("Unknown object : " . ClassUtils::getClass($object) );
        }
    }
}