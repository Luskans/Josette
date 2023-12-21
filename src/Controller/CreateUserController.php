<?php

// namespace App\Controller\User;

// use App\Entity\User;
// use DateTimeImmutable;
// use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
// use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// class CreateUserController extends AbstractController
// {
//     public function __invoke(Request $request, UserPasswordHasherInterface $passwordHasher)
//     {
//         $inputs = $request->toArray();

//         $user = new User();

//         $user->setEmail($inputs['email']);
//         // $user->setRoles($inputs['roles']);
//         $user->setPassword($passwordHasher->hashPassword($user, $inputs['password']));
        
//         return $user;
//     }
// }




namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateUserController extends AbstractController
{
    private $userRepository;
    private $passwordEncoder;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->userRepository = $userRepository;
        $this->passwordEncoder = $passwordHasher;
    }

    public function __invoke(Request $request,  EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if ($this->userRepository->findOneBy(['email' => $data['email']])) {
            throw new HttpException(400, 'Email already used.');
        }

        if ($this->userRepository->findOneBy(['name' => $data['name']])) {
            throw new HttpException(400, 'Name already taken.');
        }

        $user = new User();
        $user->setEmail($data['email']);
        $user->setPassword($this->passwordEncoder->hashPassword($user, $data['password']));
        $user->setName($data['name']);
        $user->setCreatedAt(new DateTimeImmutable());
        $user->setIsBanned(false);
        $user->setIsDeleted(false);

        $entityManager->persist($user);
        $entityManager->flush();

        // Ici, tu pourrais également générer un JWT token pour l'utilisateur
        // et le renvoyer pour un accès immédiat après l'inscription
        
        return new JsonResponse(
            // Tu peux inclure les informations que tu juges nécessaires
            ['status' => 'User created!'], 
            JsonResponse::HTTP_CREATED
        );
    }
}