<?php

namespace App\Entity;

use App\Repository\CategoryRepository;
use App\Traits\SlugifyTrait;
use App\Traits\TimeStampsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CategoryRepository::class)
 * @ORM\Table(name="categories")
 * @UniqueEntity(fields={"name"}, message="The {{ label }} is taken by another category")
 * @ORM\HasLifecycleCallbacks()
 */
class Category
{
    use TimeStampsTrait, SlugifyTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups ("ajax:posts")
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Post::class, mappedBy="category")
     */
    private $posts;

    /**
     * @ORM\Column(type="integer", options={"defaults": 0})
     */
    private $status = 0;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string
     */
    private $slug;

    public function __construct()
    {
        $this->posts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @param User|null $user
     * @return Collection|Post[]
     */
    public function getPosts(?User $user = null): Collection
    {
        if ($user instanceof User and in_array('ROLE_SUPER_ADMIN', $user->getRoles())) {
            return $this->posts;
        }
        $__posts = new ArrayCollection();
        foreach ($this->posts as $post) {
            /** @var Post $post */
            if (!$post->isDeleted()) $__posts->add($post);
        }
        return $__posts;
    }

    /**
     * @return Collection|null
     */
    public function getActivePosts(): ?Collection
    {
        $__posts = new ArrayCollection();
        foreach ($this->getPosts() as $post) {
            if ($post->getStatus() === 0) $__posts->add($post);
        }
        return $__posts;
    }

    public function addPost(Post $post): self
    {
        if (!$this->posts->contains($post)) {
            $this->posts[] = $post;
            $post->setCategory($this);
        }

        return $this;
    }

    public function removePost(Post $post): self
    {
        if ($this->posts->removeElement($post)) {
            // set the owning side to null (unless already changed)
            if ($post->getCategory() === $this) {
                $post->setCategory(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $Status): self
    {
        $this->status = $Status;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->getStatus() === 0;
    }

    /**
     * @return null|string
     */
    public function getSlug(): ?string
    {
        return $this->slug;
    }

    /**
     * @return Category
     */
    public function setSlug(): Category
    {
        $this->slug = $this->slugify($this->name);
        return $this;
    }

}
