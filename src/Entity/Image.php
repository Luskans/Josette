<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Post;
use App\Repository\ImageRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ImageRepository::class)]
#[ApiResource(
    operations: [
        // new Put(
        //     security: "object.getUser() == user",
        // ),
        // new Delete(
        //     security: "is_granted('ROLE_ADMIN') or object.getUser() == user",
        // ),
        // new Post(
        //     security: "is_granted('ROLE_USER')"
        // )
    ]
)]
class Image
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['story:read', 'user:read', 'comment:read:collection'])]
    private ?string $imagePath = null;

    #[ORM\Column(length: 255, nullable: true)]
    // #[Groups([])]
    private ?string $thumbnailPath = null;

    private $file;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['story:read', 'user:read', 'comment:read:collection'])]
    private ?string $name = null;

    #[ORM\OneToOne(inversedBy: 'image', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    #[ORM\OneToOne(inversedBy: 'image', cascade: ['persist', 'remove'])]
    private ?Story $story = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImagePath(): ?string
    {
        return $this->imagePath;
    }

    public function setImagePath(?string $imagePath): self
    {
        $this->imagePath = $imagePath;

        return $this;
    }

    public function getThumbnailPath(): ?string
    {
        return $this->thumbnailPath;
    }

    public function setThumbnailPath(?string $thumbnailPath): self
    {
        $this->thumbnailPath = $thumbnailPath;

        return $this;
    }

//     public function upload()
//     {
//         if (null === $this->getFile()) {
//             return;
//         }

//         // You must create these directories or make them writable before attempting to upload.
//         $originalDirectory = 'path/to/uploads/directory/original/';
//         $thumbnailDirectory = 'path/to/uploads/directory/thumbnail/';

//         // You can also store file name in the database if needed.
//         $fileName = md5(uniqid()) . '.' . $this->getFile()->guessExtension();

//         // Move the file to the original directory
//         $this->getFile()->move($originalDirectory, $fileName);

//         // Update the 'imagePath' property to store the file name
//         $this->imagePath = $originalDirectory . $fileName;

//         // Create and store thumbnail image
//         $thumbnailFileName = 'thumb-' . $fileName;
//         // This is where you would use a service like LiipImagine (or any other)
//         // to resize the image and save it to the thumbnail directory.
//         // Imagick or GD could be used here directly if you wanted to do it manually.
        
//         // For example, with Imagick:
//         $imagine = new \Imagick($this->imagePath);
//         $imagine->thumbnailImage(200, 0); // Width 200px and keep aspect ratio
//         $imagine->writeImage($thumbnailDirectory . $thumbnailFileName);

//         // Update the 'thumbnailPath' property to store the thumbnail file name
//         $this->thumbnailPath = $thumbnailDirectory . $thumbnailFileName;

//         // 'file' is not persisted to the database, so we clear it after use.
//         $this->file = null;
//     }

public function getName(): ?string
{
    return $this->name;
}

public function setName(?string $name): self
{
    $this->name = $name;

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
