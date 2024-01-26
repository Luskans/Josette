<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Story;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateCommentController extends AbstractController
{
    public function __invoke(Request $request,  EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $comment = new Comment();
        $comment->setContent($data['content']);

        $user = $entityManager->getRepository(User::class)->find(intval($data['userId']));
        if (!$user) {
            throw new \Exception("Utilisateur non trouvé");
        }
        $comment->setUser($user);

        $story = $entityManager->getRepository(Story::class)->find(intval($data['storyId']));
        if (!$story) {
            throw new \Exception("Histoire non trouvée");
        }
        $comment->setStory($story);

        $entityManager->persist($comment);
        $entityManager->flush();
        
        return new JsonResponse(
            // Tu peux inclure les informations que tu juges nécessaires
            ['status' => 'Comment created!'], 
            JsonResponse::HTTP_CREATED
        );
    }
}