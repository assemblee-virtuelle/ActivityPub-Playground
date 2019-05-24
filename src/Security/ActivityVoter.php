<?php

namespace App\Security;

use App\DbType\ActivityType;
use App\DbType\ActorType;
use App\Entity\Activity;
use App\Entity\Actor;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ActivityVoter extends Voter
{
    protected function supports($attribute, $subject): bool
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, ActivityType::getValues(), true)) {
            return false;
        }

        // only vote on Activity objects inside this voter
        if (!$subject instanceof Activity) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var Actor $user */
        $user = $token->getUser();
        if ('anon.' === $user) {
            // our token implementation returns 'anon.' if no user logged in,
            // whereas our code expects null in that case.
            $user = null;
        }

        // guaranteed by supports
        /** @var Activity $activity */
        $activity = $subject;

        /** @var Actor $postingActor */
        $postingActor = $activity->getActor();

        // Return true if the posting actor is the logged user
        if( $postingActor === $user ) {
            return true;
        } else {
            // If this actor may be controlled, check if the logged user is controlling it
            if( in_array($postingActor->getType(), Actor::CONTROLLABLE_ACTORS) ) {
                /** @var Actor $postingActor */
                if( $postingActor->hasControllingActor($user) ){
                    return true;
                } else {
                    // Also look for parents
                    /** @var Actor[] $controllingActors */
                    $controllingActors = $postingActor->getControllingActors();
                    foreach( $controllingActors as $controllingActor ) {
                        if( $controllingActor->hasControllingActor($user) ) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
