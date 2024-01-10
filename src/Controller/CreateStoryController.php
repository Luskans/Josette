<?php

namespace App\Controller;

use App\Entity\Story;
use App\Repository\StoryRepository;
use App\Repository\ThemeRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateStoryController extends AbstractController
{
    private $storyRepository;
    private $themeRepository;
    private $userRepository;

    public function __construct(StoryRepository $storyRepository, UserRepository $userRepository, ThemeRepository $themeRepository)
    {
        $this->storyRepository = $storyRepository;
        $this->themeRepository = $themeRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request,  EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        if ($this->storyRepository->findOneBy(['title' => $data['title']])) {
            throw new HttpException(400, 'Title already used.');
        }

        $story = new Story();
        $story->setTitle($data['title']);
        $story->setSynopsis($data['synopsis']);
        $story->setContent($data['content']);
        $story->setCreatedAt(new DateTimeImmutable());
        $story->setIsModerated(false);

        $user = $this->userRepository->find(intval($data['userId']));
        if (!$user) {
            throw new \Exception("Utilisateur non trouvé");
        }
        $story->setUser($user);

        foreach ($data['themes'] as $themeId) {
            $theme = $this->themeRepository->find($themeId);
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