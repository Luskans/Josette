<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

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

        /**
         * @var \App\Entity\User $user

         */
        $user = $token->getUser();
        if (!is_object($user)) {
            return $this->json(['message' => 'User not found'], 404);
        }

        $userData = [
            'id' => $user->getId(),
            'name' => $user->getName(),
            // 'email' => $user->getUsername(),
            'email' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'isDeleted' => $user->isIsDeleted(),
            'isBanned' => $user->isIsBanned(),
            'image' => $user->getImage(),
        ];

        return $this->json($userData);
    }
}