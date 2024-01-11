<?php

namespace App\DataFixtures;

use App\Entity\Comment;
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
        // php bin/console doctrine:fixtures:load
        $faker = \Faker\Factory::create('fr_FR');

        // Création d'un user admin
        $userAdmin = new User();
        $userAdmin->setEmail("sylvain@email.com");
        $userAdmin->setName('Sylvain');
        $userAdmin->setRoles(["ROLE_ADMIN"]);
        $userAdmin->setQuote($faker->realTextBetween($minNbChars = 50, $maxNbChars = 200, $indexSize = 2));
        $userAdmin->setDescription($faker->realTextBetween($minNbChars = 150, $maxNbChars = 400, $indexSize = 2));
        $userAdmin->setPassword($this->passwordHasher->hashPassword($userAdmin, "aaaaaa"));
        $manager->persist($userAdmin);

        for ($i = 1; $i <= 2; $i++) {
            $story = new Story();
            $story->setTitle($faker->catchPhrase());
            $story->setSynopsis($faker->realTextBetween($minNbChars = 50, $maxNbChars = 200, $indexSize = 2));
            $story->setContent($faker->realTextBetween($minNbChars = 300, $maxNbChars = 800, $indexSize = 2));
            $story->setUser($userAdmin);
            $story->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($story);
        }

        // Creation de users avec faker
        for ($i = 1; $i <= 4; $i++) {
            $user = new User();
            $user->setEmail($faker->email());
            $user->setName($faker->name());
            $user->setRoles(["ROLE_USER"]);
            $user->setQuote($faker->realTextBetween($minNbChars = 50, $maxNbChars = 200, $indexSize = 2));
            $user->setDescription($faker->realTextBetween($minNbChars = 150, $maxNbChars = 400, $indexSize = 2));
            $user->setPassword($this->passwordHasher->hashPassword($user, "aaaaaa"));
            $manager->persist($user);

            for ($j = 1; $j <= rand(2, 9); $j++) {
                $story = new Story();
                $story->setTitle($faker->catchPhrase());
                $story->setSynopsis($faker->realTextBetween($minNbChars = 50, $maxNbChars = 200, $indexSize = 2));
                $story->setContent($faker->realTextBetween($minNbChars = 300, $maxNbChars = 800, $indexSize = 2));
                $story->setUser($user);
                $story->setCreatedAt(new \DateTimeImmutable());
                $manager->persist($story);

                for ($k = 1; $k <= rand(1, 5); $k++) {
                    $commenter = new User();
                    $commenter->setName($faker->name());
                    $comment = new Comment();
                    $comment->setContent($faker->realTextBetween($minNbChars = 160, $maxNbChars = 128, $indexSize = 2));
                    $comment->setStory($story);
                    $comment->setUser($commenter);
                    $comment->setCreatedAt(new \DateTimeImmutable());
                    $comment->setIsModerated(false);
                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
