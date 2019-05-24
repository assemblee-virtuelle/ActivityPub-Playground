<?php

namespace App\Security;

use App\DbType\ActivityType;
use App\DbType\ActorType;
use App\Entity\BaseActivity;
use App\Entity\BaseActor;
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
        if (!$subject instanceof BaseActivity) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        /** @var BaseActor $user */
        $user = $token->getUser();
        if ('anon.' === $user) {
            // our token implementation returns 'anon.' if no user logged in,
            // whereas our code expects null in that case.
            $user = null;
        }

        // guaranteed by supports
        /** @var BaseActivity $activity */
        $activity = $subject;

        /** @var BaseActor $postingActor */
        $postingActor = $activity->getActor();

        // Return true if the posting actor is the logged user
        if( $postingActor === $user ) {
            return true;
        } else {
            // If this actor may be controlled, check if the logged user is controlling it
            if( in_array($postingActor->getType(), BaseActor::CONTROLLABLE_ACTORS) ) {
                /** @var BaseActor $postingActor */
                if( $postingActor->hasControllingActor($user) ){
                    return true;
                } else {
                    // Also look for parents
                    /** @var BaseActor[] $controllingActors */
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
