<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateCommentController;
use App\Controller\GetCommentsController;
use App\Repository\CommentRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter as FilterSearchFilter;

#[ORM\Entity(repositoryClass: CommentRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            normalizationContext: ['groups' => ['comment:read', 'comment:read:collection']],
            // security: "is_granted('ROLE_USER')"
        ),
        new Post(
            controller: CreateCommentController::class,
            denormalizationContext: ['groups' => ['comment:write']],
            // security: "is_granted('ROLE_USER')"
        ),
        new Patch(
            // denormalizationContext: ['groups' => ['comment:patch']],
            // security: "is_granted('ROLE_USER')"
        ),
        new Delete(
            // denormalizationContext: ['groups' => ['comment:write']],
            // security: "is_granted('ROLE_USER')"
        ),
    ],
    paginationItemsPerPage: 10
)]
#[ApiFilter(FilterSearchFilter::class, properties: ['story.id' => 'exact'])]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([ 'comment:read:collection', 'story:read', 'user:read:item'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['comment:write', 'comment:patch',  'comment:read:collection'])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups([ 'comment:read:collection'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['comment:patch',  'comment:read:collection'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups([ 'comment:read:collection'])]
    private ?bool $isModerated = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read:collection'])]
    private ?Story $story = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['comment:read:collection'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->isModerated = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
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

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isIsModerated(): ?bool
    {
        return $this->isModerated;
    }

    public function setIsModerated(?bool $isModerated): self
    {
        $this->isModerated = $isModerated;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
