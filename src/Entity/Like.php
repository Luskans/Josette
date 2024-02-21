<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateLikeController;
use DateTimeImmutable;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
#[ApiResource(
    // normalizationContext: ['groups' => ['like:read']],
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['like:read']],
        ),
        new Post(
            controller: CreateLikeController::class,
            // denormalizationContext: ['groups' => ['like:write']],
            security: "is_granted('ROLE_ADMIN') or object.owner == user"
        ),
        new Delete(
            // denormalizationContext: ['groups' => ['like:write']],
            security: "is_granted('ROLE_ADMIN') or object.owner == user"
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['user.id' => 'exact', 'story.id' => 'exact'])]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['like:read', 'story:read', 'user:read:item'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['like:read'])]
    private ?\DateTimeImmutable $likedAt = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[Groups(['like:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    #[Groups(['like:read'])]
    private ?Story $story = null;

    public function __construct()
    {
        $this->likedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLikedAt(): ?\DateTimeImmutable
    {
        return $this->likedAt;
    }

    public function setLikedAt(\DateTimeImmutable $likedAt): self
    {
        $this->likedAt = $likedAt;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getStory(): ?Story
    {
        return $this->story;
    }

    public function setStory(?Story $story): self
    {
        $this->story = $story;

        return $this;
    }
}
