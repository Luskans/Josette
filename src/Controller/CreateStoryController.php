<?php

namespace App\Controller;

use App\Entity\Story;
use App\Entity\Theme;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateStoryController extends AbstractController
{
    public function __invoke(Request $request,  EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($entityManager->getRepository(Story::class)->findOneBy(['title' => $data['title']])) {
            throw new HttpException(400, 'Title already used.');
        }

        $story = new Story();
        $story->setTitle($data['title']);
        $story->setSynopsis($data['synopsis']);
        $story->setContent($data['content']);

        $user = $entityManager->getRepository(User::class)->find(intval($data['userId']));
        if (!$user) {
            throw new \Exception("Utilisateur non trouvé");
        }
        $story->setUser($user);

        foreach ($data['themes'] as $themeId) {
            $theme = $entityManager->getRepository(Theme::class)->find($themeId);
            if (!$theme) {
                throw new \Exception("Theme non trouvé pour l'id {$themeId}");
            }
            $story->addTheme($theme);
        }

        $entityManager->persist($story);
        $entityManager->flush();
        
        return new JsonResponse(
            // Tu peux inclure les informations que tu juges nécessaires
            ['status' => 'Story created!'], 
            JsonResponse::HTTP_CREATED
        );
    }
}