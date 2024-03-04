<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
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
        
        return new JsonResponse(
            ['status' => 'User created!'], 
            JsonResponse::HTTP_CREATED
        );
    }
}