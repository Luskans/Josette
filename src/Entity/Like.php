<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Odm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\LikeRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\Get;

#[ORM\Entity(repositoryClass: LikeRepository::class)]
#[ORM\Table(name: '`like`')]
#[ApiResource(
    // normalizationContext: ['groups' => ['like:read']],
    // operations: [
    //     new Get(
    //         normalizationContext: ['groups' => ['like:read']],
    //     ),
    // ]
)]
#[ApiFilter(SearchFilter::class, properties: ['user.id' => 'exact', 'story.id' => 'exact'])]
class Like
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['story:read', 'user:read:item'])]
    private ?int $id = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $likedAt = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'likes')]

    private ?Story $story = null;

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
