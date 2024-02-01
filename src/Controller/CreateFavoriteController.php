<?php

namespace App\Controller;

use App\Entity\Favorite;
use App\Entity\Story;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateFavoriteController extends AbstractController
{
    public function __invoke(Request $request,  EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $favorite = new Favorite();

        $user = $entityManager->getRepository(User::class)->find(intval($data['userId']));
        if (!$user) {
            throw new \Exception("Utilisateur non trouvé");
        }
        $favorite->setUser($user);

        $story = $entityManager->getRepository(Story::class)->find(intval($data['storyId']));
        if (!$story) {
            throw new \Exception("Histoire non trouvée");
        }
        $favorite->setStory($story);

        $entityManager->persist($favorite);
        $entityManager->flush();
        
        return new JsonResponse(
            // Tu peux inclure les informations que tu juges nécessaires
            // ['status' => 'Favorite created!'],
            [], 
            JsonResponse::HTTP_CREATED
        );
    }
}