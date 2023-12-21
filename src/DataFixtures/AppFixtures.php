<?php

namespace App\DataFixtures;

use App\Entity\Story;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    )
    {
    }

    public function load(ObjectManager $manager): void
    {
        
        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setEmail("sylvain@email.com");
        $userAdmin->setName('Sylvain');
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setPassword($this->passwordHasher->hashPassword($userAdmin, "aaaaaa"));
        $manager->persist($userAdmin);

        for ($i = 1; $i <= 3; $i++) {
            $story = new Story();
            $story->setTitle("Story $i");
            $story->setSynopsis("Synopsis $i");
            $story->setContent("Contenu $i");
            $story->setUser($userAdmin);
            $story->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($story);
        }

        // Création d'un user
        $user = new User();
        $user->setEmail("abde@email.com");
        $user->setName('Abdé');
        $user->setRoles(["ROLE_USER"]);
        $user->setPassword($this->passwordHasher->hashPassword($user, "aaaaaa"));
        $manager->persist($user);

        for ($i = 1; $i <= 3; $i++) {
            $story = new Story();
            $story->setTitle("Story $i");
            $story->setSynopsis("Synopsis $i");
            $story->setContent("Contenu $i");
            $story->setUser($user);
            $story->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($story);
        }

        $manager->flush();
    }
}
