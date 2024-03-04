<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateFavoriteController;
use App\Repository\FavoriteRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: FavoriteRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['favorite:read']],
        ),
        new Post(
            controller: CreateFavoriteController::class,
            security: "is_granted('ROLE_ADMIN') or object.owner == user"
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object.owner == user"
        ),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['user.id' => 'exact', 'story.id' => 'exact'])]
class Favorite
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['favorite:read', 'user:read:item'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['favorite:read'])]
    private ?\DateTimeImmutable $addedAt = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[Groups(['favorite:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'favorites')]
    #[Groups(['favorite:read'])]
    private ?Story $story = null;

    public function __construct()
    {
        $this->addedAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAddedAt(): ?\DateTimeImmutable
    {
        return $this->addedAt;
    }

    public function setAddedAt(\DateTimeImmutable $addedAt): self
    {
        $this->addedAt = $addedAt;

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
