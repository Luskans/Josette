<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use App\Controller\CreateUserController;
use App\Controller\ConnectedUserController;
use App\Controller\UpdateUserController;
use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ApiResource(
    operations: [
        // new Get(
        //     uriTemplate: "/connected",
        //     controller: ConnectedUserController::class,
        //     read: false,
        //     output: false,
        //     openapiContext: [
        //         'summary' => 'Gets the currently logged in user',
        //         'security' => ['cookieAuth' => []]
        //     ],
        //     normalizationContext: ['groups' => ['user:read:connected']]
        // ),
        // new Get(
        //     uriTemplate: "/connected",
        //     controller: ConnectedUserController::class,
        //     openapiContext: [
        //         'summary' => 'Gets the currently logged in user'
        //     ],
        //     normalizationContext: ['groups' => ['user:read:connected']]
        // ),
        new Get(
            normalizationContext: ['groups' => ['user:read', 'user:read:item']]
        ),
        new GetCollection(
            normalizationContext: ['groups' => ['user:read', 'user:read:collection']]
        ),
        new Post(
            uriTemplate: "/signup",
            controller: CreateUserController::class,
            denormalizationContext: ['groups' => ['user:write']],
        ),
        new Post(
            uriTemplate: "/users/update",
            controller: UpdateUserController::class,
            deserialize: false,
            security: "is_granted('ROLE_ADMIN') or object.owner == user"
        ),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['user:read', 'story:read', 'comment:read:collection', 'like:read', 'favorite:read', 'follow:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups(['user:write'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['user:read'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups(['user:write'])]
    private ?string $password = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:write', 'user:read', 'story:read', 'comment:read:collection'])]
    private ?string $name = null;
    
    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['user:read:item'])]
    private ?string $quote = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['user:read:item'])]
    private ?string $description = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read:item'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?\DateTimeImmutable $deletedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?bool $isDeleted = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['user:read'])]
    private ?bool $isBanned = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Story::class, orphanRemoval: true)]
    #[Groups(['user:read:item'])]
    private Collection $stories;

    #[ORM\OneToOne(mappedBy: 'user', cascade: ['persist', 'remove'])]
    #[Groups(['story:read', 'user:read', 'comment:read:collection'])]
    private ?Image $image = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Comment::class, orphanRemoval: true)]
    #[Groups(['user:read:item'])]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Like::class)]
    #[Groups(['user:read:item'])]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Favorite::class)]
    #[Groups(['user:read:item'])]
    private Collection $favorites;

    #[ORM\OneToMany(mappedBy: 'follower', targetEntity: Follow::class)]
    #[Groups(['user:read:item'])]
    private Collection $imFollowing;

    #[ORM\OneToMany(mappedBy: 'followed', targetEntity: Follow::class)]
    #[Groups(['user:read:item'])]
    private Collection $whoFollowMe;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Notification::class)]
    #[Groups(['user:read:item'])]
    private Collection $notifications;

    public function __construct()
    {
        $this->stories = new ArrayCollection();
        $this->createdAt = new DateTimeImmutable();
        $this->isBanned = false;
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->favorites = new ArrayCollection();
        $this->imFollowing = new ArrayCollection();
        $this->whoFollowMe = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }
    
    public function getQuote(): ?string
    {
        return $this->quote;
    }

    public function setQuote(?string $quote): self
    {
        $this->quote = $quote;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getDeletedAt(): ?\DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?\DateTimeImmutable $deletedAt): self
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    public function isIsDeleted(): ?bool
    {
        return $this->isDeleted;
    }

    public function setIsDeleted(?bool $isDeleted): self
    {
        $this->isDeleted = $isDeleted;

        return $this;
    }

    public function isIsBanned(): ?bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(?bool $isBanned): self
    {
        $this->isBanned = $isBanned;

        return $this;
    }

    /**
     * @return Collection<int, Story>
     */
    public function getStories(): Collection
    {
        return $this->stories;
    }

    public function addStory(Story $story): self
    {
        if (!$this->stories->contains($story)) {
            $this->stories->add($story);
            $story->setUser($this);
        }

        return $this;
    }

    public function removeStory(Story $story): self
    {
        if ($this->stories->removeElement($story)) {
            // set the owning side to null (unless already changed)
            if ($story->getUser() === $this) {
                $story->setUser(null);
            }
        }

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
            $this->image->setUser(null);
        }

        // set the owning side of the relation if necessary
        if ($image !== null && $image->getUser() !== $this) {
            $image->setUser($this);
        }

        $this->image = $image;

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
            $comment->setUser($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getUser() === $this) {
                $comment->setUser(null);
            }
        }

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
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
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
            $favorite->setUser($this);
        }

        return $this;
    }

    public function removeFavorite(Favorite $favorite): self
    {
        if ($this->favorites->removeElement($favorite)) {
            // set the owning side to null (unless already changed)
            if ($favorite->getUser() === $this) {
                $favorite->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Follow>
     */
    public function getImFollowing(): Collection
    {
        return $this->imFollowing;
    }

    public function addImFollowing(Follow $imFollowing): self
    {
        if (!$this->imFollowing->contains($imFollowing)) {
            $this->imFollowing->add($imFollowing);
            $imFollowing->setFollower($this);
        }

        return $this;
    }

    public function removeImFollowing(Follow $imFollowing): self
    {
        if ($this->imFollowing->removeElement($imFollowing)) {
            // set the owning side to null (unless already changed)
            if ($imFollowing->getFollower() === $this) {
                $imFollowing->setFollower(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Follow>
     */
    public function getWhoFollowMe(): Collection
    {
        return $this->whoFollowMe;
    }

    public function addWhoFollowMe(Follow $whoFollowMe): self
    {
        if (!$this->whoFollowMe->contains($whoFollowMe)) {
            $this->whoFollowMe->add($whoFollowMe);
            $whoFollowMe->setFollowed($this);
        }

        return $this;
    }

    public function removeWhoFollowMe(Follow $whoFollowMe): self
    {
        if ($this->whoFollowMe->removeElement($whoFollowMe)) {
            // set the owning side to null (unless already changed)
            if ($whoFollowMe->getFollowed() === $this) {
                $whoFollowMe->setFollowed(null);
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
            $notification->setUser($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getUser() === $this) {
                $notification->setUser(null);
            }
        }

        return $this;
    }

}
