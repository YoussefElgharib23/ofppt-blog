<?php

namespace App\Entity;

use App\Repository\SettingRepository;
use App\Traits\TimeStampsTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=SettingRepository::class)
 * @ORM\Table(name="settings")
 * @Vich\Uploadable()
 */
class Setting
{
    use TimeStampsTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $logo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $homeImage;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $applicationName;

    /**
     * @Vich\UploadableField(mapping="settings", fileNameProperty="logo")
     * @var File|null
     */
    private $imageFileLogo;

    /**
     * @Vich\UploadableField(mapping="settings", fileNameProperty="homeImage")
     * @var File|null
     */
    private $imageFileHome;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $Favicon;

    /**
     * @Vich\UploadableField(mapping="settings", fileNameProperty="Favicon")
     * @var File|null
     */
    private $favIconImageFile;

    /**
     * @return File|null
     */
    public function getFavIconImageFile(): ?File
    {
        return $this->favIconImageFile;
    }

    /**
     * @param File|null $favIconImageFile
     * @return void
     */
    public function setFavIconImageFile(?File $favIconImageFile): void
    {
        $this->favIconImageFile = $favIconImageFile;
        if (null !== $favIconImageFile) {
            $this->updateTimeStampable();
        }
    }

    /**
     * @param File|UploadedFile|null $imageFile
     */
    public function setImageFileLogo(?File $imageFile = null): void
    {
        $this->imageFileLogo = $imageFile;

        if (null !== $imageFile) {
            $this->updateTimeStampable();
        }
    }

    public function getImageFileLogo(): ?File
    {
        return $this->imageFileLogo;
    }

    public function getImageFileHome(): ?File
    {
        return $this->imageFileHome;
    }

    /**
     * @param File|UploadedFile|null $imageFile
     */
    public function setImageFileHome(?File $imageFile = null): void
    {
        $this->imageFileHome = $imageFile;

        if (null !== $imageFile) {
            $this->updateTimeStampable();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLogo(): ?string
    {
        return $this->logo;
    }

    public function setLogo(?string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function getHomeImage(): ?string
    {
        return $this->homeImage;
    }

    public function setHomeImage(?string $homeImage): self
    {
        $this->homeImage = $homeImage;

        return $this;
    }

    public function getApplicationName(): ?string
    {
        return $this->applicationName;
    }

    public function setApplicationName(string $applicationName): self
    {
        $this->applicationName = $applicationName;

        return $this;
    }

    public function getFavicon(): ?string
    {
        return $this->Favicon;
    }

    public function setFavicon(?string $Favicon): self
    {
        $this->Favicon = $Favicon;

        return $this;
    }
}
