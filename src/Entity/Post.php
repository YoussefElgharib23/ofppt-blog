<?php

namespace App\Entity;

use App\Repository\PostRepository;
use App\Traits\SlugifyTrait;
use App\Traits\TimeStampsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=PostRepository::class)
 * @ORM\Table(name="posts")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable()
 */
class Post
{
    const STATUS = [
        'Active',
        'Inactive'
    ];

    use TimeStampsTrait, SlugifyTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("ajax:posts")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("ajax:posts")
     */
    private $title;

    /**
     * @ORM\Column(type="text")
     * @Groups ("ajax:posts")
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="posts")
     * @Groups ("ajax:posts")
     */
    private $category;

    /**
     * @Vich\UploadableField(mapping="posts", fileNameProperty="imageName")
     * @Assert\Image(mimeTypes="image/jpeg")
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups ("ajax:posts")
     * @var string|null
     */
    private $imageName;

    /**
     * @var string
     * @Groups("ajax:posts")
     */
    private $imageNameCached;

    /**
     * @ORM\ManyToOne(targetEntity=User::class)
     * @ORM\JoinColumn(nullable=false)
     * @Groups("ajax:posts")
     */
    private $user;

    /**
     * @ORM\Column(type="integer", options={"default"=0})
     */
    private $status = 0;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="Post", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="post", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Dislike::class, mappedBy="post", orphanRemoval=true)
     */
    private $dislikes;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="post")
     */
    private $notifications;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups("ajax:posts")
     */
    private $minDescription;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->dislikes = new ArrayCollection();
        $this->notifications = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getFormattedCreatedAt()
    {
        return $this->formattedCreatedAt;
    }

    /**
     * @ORM\PrePersist()
     */
    public function setFormattedCreatedAt(): void
    {
        $this->formattedCreatedAt = date_format(new \DateTimeImmutable(), 'd M, Y');
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile|null $imageFile
     */
    public function setImageFile(?File $imageFile = null): void
    {
        $this->imageFile = $imageFile;

        if (null !== $imageFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            $this->updated_at = new \DateTimeImmutable();
        }
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $User): self
    {
        $this->user = $User;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function setStatus(string $Status): self
    {
        $this->status = $Status;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getImageNameCached(): ?string
    {
        return $this->imageNameCached;
    }

    /**
     * @param string|null $imageNameCached
     * @return Post
     */
    public function setImageNameCached(?string $imageNameCached): Post
    {
        $this->imageNameCached = $imageNameCached;
        return $this;
    }

    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return Collection|Comment[]|null
     */
    public function getActiveUserComments(): ?Collection
    {
        $__comments = new ArrayCollection();
        foreach ($this->getComments() as $comment) {
            if ($comment->getUser()->isStatus() && $comment->getDeletedAt() === null) $__comments->add($comment);
        }
        return $__comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): self
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function isActive(): bool
    {
        return $this->getStatus() === 0;
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    /**
     * @return Collection|null|Like[]
     */
    public function getActiveUserLikes(): ?Collection
    {
        $_likes = new ArrayCollection();
        foreach ($this->getLikes() as $like) if ($like->getUser()->isStatus()) $_likes->add($like);
        return $_likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
            $like->setPost($this);
        }

        return $this;
    }

    public function removeLike(Like $like): self
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getPost() === $this) {
                $like->setPost(null);
            }
        }

        return $this;
    }

    /**
     * TO CHECK IF THE POST IS LIKED BY THE USER
     *
     * @param User $user
     * @return bool
     */
    public function isLikedByUser(User $user): bool
    {
        foreach ($this->getLikes() as $like) if ($like->getUser() === $user) return true;
        return false;
    }

    /**
     * TO CHECK IF THE POST IS DISLIKED BY THE USER
     *
     * @param User $user
     * @return bool
     */
    public function isDislikedByUser(User $user): bool
    {
        foreach ($this->getDislikes() as $dislike) if ($dislike->getUser() === $user) return true;
        return false;
    }

    /**
     * @return Collection|Dislike[]
     */
    public function getDislikes(): Collection
    {
        return $this->dislikes;
    }

    /**
     * @return Collection|Dislike[]
     */
    public function getActiveDislike(): Collection
    {
        $_dislikes = new ArrayCollection();
        foreach ($this->getDislikes() as $dislike) if ($dislike->getUser()->isStatus()) $_dislikes->add($dislike);
        return $_dislikes;
    }

    public function addDislike(Dislike $dislike): self
    {
        if (!$this->dislikes->contains($dislike)) {
            $this->dislikes[] = $dislike;
            $dislike->setPost($this);
        }

        return $this;
    }

    public function removeDislike(Dislike $dislike): self
    {
        if ($this->dislikes->removeElement($dislike)) {
            // set the owning side to null (unless already changed)
            if ($dislike->getPost() === $this) {
                $dislike->setPost(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Notification[]
     */
    public function getNotifications(): Collection
    {
        return $this->notifications;
    }

    public function addNotification(Notification $notification): self
    {
        if (!$this->notifications->contains($notification)) {
            $this->notifications[] = $notification;
            $notification->setPost($this);
        }

        return $this;
    }

    public function removeNotification(Notification $notification): self
    {
        if ($this->notifications->removeElement($notification)) {
            // set the owning side to null (unless already changed)
            if ($notification->getPost() === $this) {
                $notification->setPost(null);
            }
        }

        return $this;
    }

    public function getMinDescription(): ?string
    {
        return $this->minDescription;
    }

    public function setMinDescription(?string $minDescription): self
    {
        $this->minDescription = $minDescription;

        return $this;
    }
}