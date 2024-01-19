<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Repository\FollowRepository;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

use Symfony\Component\HttpKernel\Exception\HttpException;

class CreateFollowController extends AbstractController
{
    private $followRepository;
    private $userRepository;

    public function __construct(FollowRepository $followRepository, UserRepository $userRepository)
    {
        $this->followRepository = $followRepository;
        $this->userRepository = $userRepository;
    }

    public function __invoke(Request $request,  EntityManagerInterface $entityManager): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Empèche le cas en front
        // if ($this->followRepository->findOneByFollowerAndFollowed($data['follower'], $data['followed'])) {
        //     throw new HttpException(400, 'Author already followed.');
        // }

        $follower = $this->userRepository->find($data['follower']);
        $followed = $this->userRepository->find($data['followed']);
        $follow = new Follow();
        $follow->setFollower($follower);
        $follow->setFollowed($followed);
        $follow->setCreatedAt(new DateTimeImmutable());

        $entityManager->persist($follow);
        $entityManager->flush();

        $newFollowData = [
            'id' => $followed->getId(),
            'name' => $followed->getName(),
            'image' => $followed->getImage(),
        ];
        
        return new JsonResponse(
            // Tu peux inclure les informations que tu juges nécessaires
            ['follow' => $newFollowData], 
            JsonResponse::HTTP_CREATED
        );
    }
}