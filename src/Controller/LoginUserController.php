<?php
namespace App\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LoginUserController
{
    private $userRepository;

    public function construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function invoke(Request $request, $email): Response
    {
        $user = $this->userRepository->findOneByEmail($email);

        if (!$user) {
            return new Response(json_encode(['error' => 'User not found']), 404);
        }

        // Serialize the user object to JSON or any preferred format
        // Then return a Response with proper headers
        // ...
    }
}
