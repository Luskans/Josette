<?php
// namespace App\Events;

// use App\Entity\User;
// use App\Repository\UserRepository;
// use Symfony\Component\HttpFoundation\RequestStack;
// use Symfony\Component\Security\Core\User\UserInterface;
// use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

// class JWTCreatedListener  {
//     /**
//      * @param JWTCreatedEvent $event
//      *
//      * @return void
//      */
//     public function onJWTCreated(JWTCreatedEvent $event)
//     {
//         $payload = $event->getData();
//         $user = $event->getUser();
//         if (!$user instanceof UserInterface) {
//             throw new \Exception("User not found", 500);
//         }
//         $payload['username'] = $user->getEmail();
//         $payload['id'] = $user->getId();
//         $header = $event->getHeader();
//         $event->setData($payload);
//         $event->setHeader($header);
//     }
// }



// namespace App\EventListener;

// use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

// class JWTCreatedListener
// {
//     public function onJWTCreated(JWTCreatedEvent $event)
//     {
//         // Récupère l'utilisateur pour pouvoir ajouter plus d'infos dans le JWT
//         $user = $event->getUser();

//         // Créer ton tableau de données personnalisées (tu peux ajouter ici tout ce qui est sans risque)
//         $customData = [
//             'userId'   => $user->getId(),
//             'email'    => $user->getEmail(),
//             // Ajoute ici d'autres données utiles
//         ];

//         // Mise à jour des données du JWT
//         $payload = array_merge(
//             $event->getData(),
//             $customData
//         );

//         // Réaffecte les nouvelles données personnalisées
//         $event->setData($payload);
//     }
// }

namespace App\Events;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();
        $user = $event->getUser();
        // if (!$user instanceof UserInterface) {
        //     throw new \Exception("User not found", 500);
        // }

        $payload['id'] = $user->getId();
        // $payload['email'] = $user->getEmail();
        $payload['roles'] = $user->getRoles();
        $payload['name'] = $user->getName();
        // $payload['quote'] = $user->getQuote();
        // $payload['description'] = $user->getDescription();
        $payload['createdAt'] = $user->getCreatedAt();
        // $payload['updatedAt'] = $user->getUpdatedAt();
        $payload['isDeleted'] = $user->isIsDeleted();
        $payload['isBanned'] = $user->isIsBanned();
        $payload['image'] = $user->getImage()->getName();
        // $payload['stories'] = $user->getStories();
        // $payload['comments'] = $user->getComments();
        // $payload['likes'] = $user->getLikes();
        // $payload['favorites'] = $user->getFavorites();
        // $payload['imFollowing'] = $user->getImFollowing();
        // $payload['whoFollowMe'] = $user->getWhoFollowMe();
        // $payload['notifications'] = $user->getNotifications();
        
        $event->setData($payload);
    }
}