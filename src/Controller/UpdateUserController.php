<?php

namespace App\Controller;

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
    public function __invoke(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        // dd($request->$request->get('profilId'));
        $user = $entityManager->getRepository(User::class)->find(intval($request->request->get('profilId')));
        // $user = $entityManager->getRepository(User::class)->find($request->attributes->get('profilId'));

        if (!isset($user)) {
            throw new \Exception("Utilisateur non trouvé");
        }
        $user->setQuote($request->request->get('quote'));
        $user->setDescription($request->request->get('description'));
        $user->setUpdatedAt(new DateTimeImmutable());

        $file = $request->files->get('image'); // Récupération du fichier
        if ($file) {
            // $image = $entityManager->getRepository(Image::class)->findOneBy(['user.id' => intval($request->request->get('profilId'))]);
            $image = $entityManager->getRepository(Image::class)->findOneBy(['user' => $user]);

            if (isset($image)) {
                $image->setFile($file);
                $user->setImage($image);

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