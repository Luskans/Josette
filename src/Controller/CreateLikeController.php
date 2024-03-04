<?php

namespace App\Controller;

use App\Entity\Like;
use App\Entity\Story;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateLikeController extends AbstractController
{
    public function __invoke(Request $request,  EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $like = new Like();

        $user = $entityManager->getRepository(User::class)->find(intval($data['userId']));
        if (!$user) {
            throw new \Exception("Utilisateur non trouvé");
        }
        $like->setUser($user);

        $story = $entityManager->getRepository(Story::class)->find(intval($data['storyId']));
        if (!$story) {
            throw new \Exception("Histoire non trouvée");
        }
        $like->setStory($story);

        $entityManager->persist($like);
        $entityManager->flush();
        
        return new JsonResponse(
            ['status' => 'Like created!'], 
            JsonResponse::HTTP_CREATED
        );
    }
}