<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\StoryRepository;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StoryRepository::class)]
#[ApiResource(
    operations: [
        new Get(
            normalizationContext: ['groups' => ['story:read']],
            security: "is_granted('ROLE_USER')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['story:read']],
            security: "is_granted('ROLE_USER')"
        ),
        new Patch(
            security: "is_granted('ROLE_ADMIN') or object.getUser() == user",
        ),
        new Delete(
            security: "is_granted('ROLE_ADMIN') or object.getUser() == user",
        ),
        new Post(
            denormalizationContext: ['groups' => ['story:write']],
            normalizationContext: ['groups' => ['story:read']],
            security: "is_granted('ROLE_USER')"
        )
    ]
)]
class Story
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['story:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['story:read', 'story:write', 'user:read'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['story:read', 'story:write', 'user:read'])]
    private ?string $synopsis = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['story:read', 'story:write', 'user:read'])]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['story:read', 'user:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['story:read', 'user:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['story:read', 'user:read'])]
    private ?bool $isModerated = null;

    #[ORM\ManyToOne(inversedBy: 'stories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['story:read'])]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'story', cascade: ['persist', 'remove'])]
    #[Groups(['story:read'])]
    private ?Image $image = null;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->isModerated = false;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(?string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
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

    public function setCreatedAt(?\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): self
    {
        // unset the owning side of the relation if necessary
        if ($image === null && $this->image !== null) {
            $this->image->setStory(null);
        }

        // set the owning side of the relation if necessary
        if ($image !== null && $image->getStory() !== $this) {
            $image->setStory($this);
        }

        $this->image = $image;

        return $this;
    }
}
