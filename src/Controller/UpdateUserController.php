<?php

namespace App\Controller;

use App\Entity\Story;
use App\Entity\Theme;
use App\Entity\User;
use App\Entity\Image;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpException;

class UpdateUserController extends AbstractController
{
    public function __invoke(Request $request,  EntityManagerInterface $entityManager): JsonResponse
    {

        $user = $entityManager->getRepository(User::class)->find(intval($request->request->get('profilId')));
        $user->setQuote($request->request->get('quote'));
        $user->setDescription($request->request->get('description'));

        $file = $request->files->get('image'); // Récupération du fichier
        if ($file) {
            $image = $entityManager->getRepository(Image::class)->findOneByUserId(['user.id' => $request->request->get('profilId')]);

            if ($image) {
                $image->setFile($file);

            } else {
                $newImage = new Image();
                $newImage->setFile($file);
                $user->setImage($newImage);
            }

        }

        $entityManager->persist($user);
        $entityManager->flush();
        
        return new JsonResponse(
            ['status' => 'User updated!'], 
            JsonResponse::HTTP_CREATED
        );
    }
}