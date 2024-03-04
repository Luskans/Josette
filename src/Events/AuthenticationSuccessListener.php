<?php

namespace App\Events;

use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use Symfony\Component\HttpFoundation\Cookie;

class AuthenticationSuccessListener
{
    public function onAuthenticationSuccess(AuthenticationSuccessEvent $event)
    {
        $response = $event->getResponse();
        $data = $event->getData();
        $token = $data['token'];
        
        // Création du cookie httpOnly avec le JWT
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