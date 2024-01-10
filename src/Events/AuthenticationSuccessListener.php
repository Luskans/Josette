<?php

// namespace App\Events;

// use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
// use Symfony\Component\Security\Core\User\UserInterface;
// use Symfony\Component\HttpFoundation\Cookie;
// use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\HttpFoundation\RequestStack;


// class AuthenticationSuccessListener
// {
//     public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
//     {
//         $response = $event->getResponse();
//         $data = $event->getData();
//         $token = $data['token'];
        
//         // Création du cookie httpOnly avec le JWT
//         $cookie = new Cookie('BEARER', $token, (new \DateTime())->add(new \DateInterval('PT2H')), '/', null, true, true);
        
//         // Ajout du cookie à la réponse
//         $response->headers->setCookie($cookie);
        
//         // Tu peux modifier le corps de la réponse si tu veux
//         $event->setData([
//             'message' => 'Authentication successful!',
//             // Autres données si nécessaire...
//         ]);

        
//         $payload = $event->getData();
//         $user = $event->getUser();
//         if (!$user instanceof UserInterface) {
//             throw new \Exception("User not found", 500);
//         }

//         $payload['id'] = $user->getId();
//         $payload['name'] = $user->getName();
//         // $payload['roles'] = $user->getRoles();
//         $payload['isDeleted'] = $user->isIsDeleted();
//         $payload['isBanned'] = $user->isIsBanned();
//         $payload['image'] = $user->getImage();
        
//         $event->setData($payload);
//     }
// }