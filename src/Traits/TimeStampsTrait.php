<?php

namespace App\Traits;

use Symfony\Component\Serializer\Annotation\Groups;

Trait TimeStampsTrait
{
    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     * @Groups ("ajax:posts")
     */
    private $created_at;


    /**
     * @ORM\Column(type="datetime", options={"default": "CURRENT_TIMESTAMP"})
     */
    private $updated_at;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $deleted_at;

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt(\DateTimeInterface $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt(\DateTimeInterface $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @return mixed
     */
    public function getDeletedAt(): ?\DateTimeInterface
    {
        return $this->deleted_at;
    }

    /**
     * @param mixed $deleted_at
     */
    public function setDeletedAt(?\DateTimeInterface $deleted_at): void
    {
        $this->deleted_at = $deleted_at;
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function updateTimeStampable()
    {
        if (null === $this->getCreatedAt()) $this->setCreatedAt(new \DateTimeImmutable());

        $this->setUpdatedAt(new \DateTimeImmutable());
    }

    public function delete()
    {
        $this->setDeletedAt(new \DateTimeImmutable());
    }

    public function restore()
    {
        $this->setDeletedAt(null);
    }

    public function isDeleted(): bool
    {
        return $this->getDeletedAt() != null;
    }
}