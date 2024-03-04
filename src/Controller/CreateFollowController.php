<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateFollowController extends AbstractController
{
    public function __invoke(Request $request,  EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $follow = new Follow();

        $follower = $entityManager->getRepository(User::class)->find(intval($data['followerId']));
        if (!$follower) {
            throw new \Exception("Utilisateur qui suit non trouvé");
        }
        $follow->setFollower($follower);

        $followed = $entityManager->getRepository(User::class)->find(intval($data['followedId']));
        if (!$followed) {
            throw new \Exception("Utilisateur à suivre non trouvé");
        }
        $follow->setFollowed($followed);

        $entityManager->persist($follow);
        $entityManager->flush();
        
        return new JsonResponse(
            ['status' => 'Follow created!'], 
            JsonResponse::HTTP_CREATED
        );
    }
}