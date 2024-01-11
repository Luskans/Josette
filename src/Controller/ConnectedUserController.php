<?php

// namespace App\Controller;

// use Symfony\Component\HttpFoundation\JsonResponse;
// use Symfony\Component\Routing\Annotation\Route;
// use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

// class CurrentUserController
// {
//     private $tokenStorage;

//     public function __construct(TokenStorageInterface $tokenStorage)
//     {
//         $this->tokenStorage = $tokenStorage;
//     }

//     public function currentUser(): JsonResponse
//     {
//         $token = $this->tokenStorage->getToken();
//         if ($token === null) {
//             return $this->json(null, 401);
//         }

//         $user = $token->getUser();

//         // Si ton Token implémente UserInterface, tu peux directement appeler les méthodes dessus
//         if (!is_object($user)) {
//             return $this->json(null, 401);
//         }

//         $userData = [
//             'id' => $user->getId(),
//             'name' => $user->getName(),
//             'email' => $user->getUsername(),
//             'roles' => $user->getRoles(),
//             'isDeleted' => $user->isIsDeleted(),
//             'isBanned' => $user->isIsBanned(),
//             'image' => $user->getImage(),
//         ];

//         return $this->json($userData);
//     }
// }


namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use ApiPlatform\Metadata\ApiResource;

class ConnectedUserController extends AbstractController
{
    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function __invoke(): JsonResponse
    {
        $token = $this->tokenStorage->getToken();
        if ($token === null) {
            return $this->json(['message' => 'User not found'], 404);
        }

        $user = $token->getUser();
        if (!is_object($user)) {
            return $this->json(['message' => 'User not found'], 404);
        }

        $userData = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            'email' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'isDeleted' => $user->isIsDeleted(),
            'isBanned' => $user->isIsBanned(),
            'image' => $user->getImage(),
        ];

        return $this->json($userData);
    }
}