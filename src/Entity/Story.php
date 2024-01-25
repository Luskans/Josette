<?php

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter as FilterOrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter as FilterSearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Controller\CreateStoryController;
use App\Repository\StoryRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: StoryRepository::class)]
#[ApiResource(
    // normalizationContext: ['groups' => ['story:read']],
    operations: [
        new Get(
            normalizationContext: ['groups' => ['story:read', 'story:read:item']],
            // security: "is_granted('ROLE_USER')"
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['story:read', 'story:read:collection']],
            // security: "is_granted('ROLE_USER')"
        ),
        new Post(
            controller: CreateStoryController::class,
            denormalizationContext: ['groups' => ['story:write']],
            // normalizationContext: ['groups' => ['story:read']],
            // security: "is_granted('ROLE_USER')"
        ),
        // new Patch(
        //     security: "is_granted('ROLE_ADMIN') or object.getUser() == user",
        // ),
        // new Delete(
        //     security: "is_granted('ROLE_ADMIN') or object.getUser() == user",
        // ),
    ],
    paginationClientEnabled: true
    // attributes: [
    //     "pagination_items_per_page" => 5,
    //     "pagination_maximum_items_per_page" => 50,
    //     "pagination_client_items_per_page" => true,
    // ],
)]
#[ApiFilter(FilterSearchFilter::class, properties: ['title' => 'partial'])]
#[ApiFilter(FilterOrderFilter::class, properties: ['createdAt', 'viewCount'], arguments: ['orderParameterName' => 'order'])]
class Story
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['story:read', 'user:read:item'])]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['story:read', 'user:read:item'])]
    private ?string $title = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['story:read:collection'])]
    private ?string $synopsis = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['story:read', 'user:read:item'])]
    private ?string $content = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['story:read', 'user:read:item'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['story:read', 'user:read:item'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['story:read', 'user:read:item'])]
    private ?bool $isModerated = null;

    #[ORM\ManyToOne(inversedBy: 'stories')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['story:read'])]
    private ?User $user = null;

    #[ORM\OneToOne(mappedBy: 'story', cascade: ['persist', 'remove'])]
    #[Groups(['story:read:item', 'user:read:item'])]
    private ?Image $image = null;

    #[ORM\ManyToMany(targetEntity: Theme::class, mappedBy: 'stories')]
    #[Groups(['story:read', 'user:read:item'])]
    private Collection $themes;

    #[ORM\OneToMany(mappedBy: 'story', targetEntity: Comment::class, orphanRemoval: true)]
    #[Groups(['story:read', 'user:read:item'])]
    private Collection $comments;

    #[ORM\Column(nullable: true)]
    #[Groups(['story:read', 'user:read:item'])]
    private ?int $viewCount = null;

    #[ORM\OneToMany(mappedBy: 'story', targetEntity: Like::class)]
    #[Groups(['story:read', 'user:read:item'])]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'story', targetEntity: Favorite::class)]
    private Collection $favorites;

    #[ORM\OneToMany(mappedBy: 'story', targetEntity: Notification::class)]
    private Collection $notifications;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->isModerated = false;
        $this->themes = new ArrayCollection();
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->viewCount = 0;
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

    /**
     * @return Collection<int, Theme>
     */
    public function getThemes(): Collection
    {
        return $this->themes;
    }

    public function addTheme(Theme $theme): self
    {
        if (!$this->themes->contains($theme)) {
            $this->themes->add($theme);
            $theme->addStory($this);
        }

        return $this;
    }

    public function removeTheme(Theme $theme): self
    {
        if ($this->themes->removeElement($theme)) {
            $theme->removeStory($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setStory($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getStory() === $this) {
                $comment->setStory(null);
            }
        }

        return $this;
    }

    public function getViewCount(): ?int
    {
        return $this->viewCount;
    }

    public function setViewCount(?int $viewCount): self
    {
        $this->viewCount = $viewCount;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setStory($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getStory() === $this) {
                $like->setStory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Favorite>
     */
    public function getFavorites(): Collection
    {
        return $this->favorites;
    }

    public function addFavorite(Favorite $favorite): self
    {
        if (!$this->favorites->contains($favorite)) {
            $this->favorites->add($favorite);
            $favorite->setStory($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getStory() === $this) {
                $favorite->setStory(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Notification>
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications->add($notification);
            $notification->setStory($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getStory() === $this) {
                $notification->setStory(null);
            }
        }

        return $this;
    }
}
