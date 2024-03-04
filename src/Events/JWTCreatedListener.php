<?php

namespace App\Events;

use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();
        /**
         * @var \App\Entity\User $user 
         */
        $user = $event->getUser();
       
        $payload['id'] = $user->getId();
        $payload['roles'] = $user->getRoles();
        $payload['name'] = $user->getName();
        $payload['createdAt'] = $user->getCreatedAt();
        $payload['isDeleted'] = $user->isIsDeleted();
        $payload['isBanned'] = $user->isIsBanned();
        $payload['image'] = $user->getImage()->getName();
        
        $event->setData($payload);
    }
}