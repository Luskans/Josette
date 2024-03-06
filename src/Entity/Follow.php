<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter as FilterSearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateFollowController;
use App\Repository\FollowRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FollowRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['follow:read']],
        ),
        new Post(
            controller: CreateFollowController::class,
            security: "is_granted('ROLE_USER')",
        ),
        new Delete(
            security: "is_granted('ROLE_USER')",
        ),
    ]
)]
#[ApiFilter(FilterSearchFilter::class, properties: ['follower.id' => 'exact', 'followed.id' => 'exact'])]
class Follow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read:item', 'follow:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['follow:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'imFollowing')]
    #[Groups(['follow:read'])]
    private ?User $follower = null;

    #[ORM\ManyToOne(inversedBy: 'whoFollowMe')]
    #[Groups(['follow:read'])]
    private ?User $followed = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFollower(): ?User
    {
        return $this->follower;
    }

    public function setFollower(?User $follower): self
    {
        $this->follower = $follower;

        return $this;
    }

    public function getFollowed(): ?User
    {
        return $this->followed;
    }

    public function setFollowed(?User $followed): self
    {
        $this->followed = $followed;

        return $this;
    }
}
