<?php

namespace App\Events;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RequestStack;


class AuthenticationSuccessListener
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $response = $event->getResponse();
        $data = $event->getData();
        $token = $data['token'];
        
        // Création du cookie httpOnly avec le JWT
        // $cookie = new Cookie('BEARER', $token, (new \DateTime())->add(new \DateInterval('PT2H')), "localhost:5173", '/', null, true, true);
        // $cookie = new Cookie('BEARER', $token, (new \DateTime())->add(new \DateInterval('PT2H')), "localhost", null, true, false);
        // $cookie = new Cookie('BEARER', $token, (new \DateTime())->add(new \DateInterval('PT2H')), '/', null, true, true, false, 'lax');
        $cookie = new Cookie('BEARER', $token, (new \DateTime())->add(new \DateInterval('PT2H')), 'localhost', null, false, true, false, 'Lax');

        // Ajout du cookie à la réponse
        $response->headers->setCookie($cookie);
        
        // Tu peux modifier le corps de la réponse si tu veux
        $event->setData([
            'message' => 'Authentication successful!',
            // Autres données si nécessaire...
        ]);
    }
}