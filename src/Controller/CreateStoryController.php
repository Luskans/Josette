<?php

namespace App\Controller;

use App\Entity\Story;
use App\Entity\Theme;
use App\Entity\User;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateStoryController extends AbstractController
{

    public function __invoke(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {

        if ($entityManager->getRepository(Story::class)->findOneBy(['title' => $request->request->get('title')])) {
            throw new HttpException(400, 'Title already used.');
        }

        $story = new Story();
        $story->setTitle($request->request->get('title'));
        $story->setSynopsis($request->request->get('synopsis'));
        $story->setContent($request->request->get('content'));

        $user = $entityManager->getRepository(User::class)->find(intval($request->request->get('userId')));
        if (!$user) {
            throw new \Exception("Utilisateur non trouvé");
        }
        $story->setUser($user);

        $file = $request->files->get('image'); // Récupération du fichier
        if ($file) {
            $image = new Image();
            $image->setFile($file);
            $story->setImage($image);
        }

        $theme1 = $entityManager->getRepository(Theme::class)->find($request->request->get('theme1'));
        if (!$theme1) {
            throw new \Exception("Theme non trouvé pour l'id du thème 1");
        }
        $story->addTheme($theme1);

        if ($request->request->get('theme2')) {
            $theme2 = $entityManager->getRepository(Theme::class)->find($request->request->get('theme2'));
            if (!$theme2) {
                throw new \Exception("Theme non trouvé pour l'id du thème 2");
            }
            $story->addTheme($theme2);
        }

        $entityManager->persist($story);
        $entityManager->flush();
        
        return new JsonResponse(
            ['status' => 'Story created!'], 
            JsonResponse::HTTP_CREATED
        );
    }
}