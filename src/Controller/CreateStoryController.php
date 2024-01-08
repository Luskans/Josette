<?php

namespace App\Controller;

use App\Entity\Story;
use App\Repository\StoryRepository;
use App\Repository\ThemeRepository;
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

    public function __construct(StoryRepository $storyRepository, ThemeRepository $themeRepository)
    {
        $this->storyRepository = $storyRepository;
        $this->themeRepository = $themeRepository;
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
        $story->setCreatedAt(new DateTimeImmutable());
        $story->setIsModerated(false);
        $story->setUser($data['userId']);

        $theme1 = $this->themeRepository->findOneBy(['name' => $data['theme1']]);
        $story->addTheme($theme1);

        if (isset($data['theme2'])) {
            $story->addTheme($data['theme2']);
        }

        $entityManager->persist($story);
        $entityManager->flush();

        // Ici, tu pourrais également générer un JWT token pour l'utilisateur
        // et le renvoyer pour un accès immédiat après l'inscription
        
        return new JsonResponse(
            // Tu peux inclure les informations que tu juges nécessaires
            ['status' => 'Story created!'], 
            JsonResponse::HTTP_CREATED
        );
    }
}