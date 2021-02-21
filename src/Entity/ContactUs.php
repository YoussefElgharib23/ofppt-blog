<?php

namespace App\Entity;

use App\Repository\ContactUsRepository;
use App\Traits\TimeStampsTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=ContactUsRepository::class)
 * @Vich\Uploadable()
 * @ORM\HasLifecycleCallbacks()
 */
class ContactUs
{
    use TimeStampsTrait;

    const TYPES = [
        'A general enquiry' => 'generale',
        'I have a problem need help' => 'problem'
    ];

    const CATEGORY = [
        'Generale' => 'generale',
        'Account' => 'account'
    ];

    const PRIORITIES = [
        'Normal' => 'normal',
        'Important' => 'important',
        'High priority' => 'high priority'
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("contact:ajax")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     * @Groups("contact:ajax")
     */
    private $type;

    /**
     * @ORM\Column(type="string")
     * @Groups("contact:ajax")
     */
    private $category;

    /**
     * @ORM\Column(type="string", length=255)string
     * @Groups("contact:ajax")
     */
    private $description;

    /**
     * @ORM\Column(type="text")
     * @Groups("contact:ajax")
     */
    private $details;

    /**
     * @Vich\UploadableField(mapping="posts", fileNameProperty="imageName")
     * @Assert\Image(mimeTypes="image/jpeg")
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default": null})
     * @Groups("contact:ajax")
     */
    private $imageName;

    /**
     * @ORM\Column(type="integer")
     * @Groups("contact:ajax")
     */
    private $priority;

    /**
     * @ORM\OneToOne(targetEntity=ReplyContactUs::class, mappedBy="contactUs", cascade={"persist", "remove"})
     * @Groups("contact:ajax")
     */
    private $replyContactUs;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     * @var bool
     */
    private $isSeen = false;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("contact:ajax")
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("contact:ajax")
     */
    private $lastName;

    /**
     * @var string
     * @Groups("contact:ajax")
     */
    private $formattedCreatedAt;

    /**
     * @var string
     * @Groups("contact:ajax")
     */
    private $imgTargetPath;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("contact:ajax")
     */
    private $email;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(string $category): self
    {
        $this->category = $category;

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

    public function getDetails(): ?string
    {
        return $this->details;
    }

    public function setDetails(string $details): self
    {
        $this->details = $details;

        return $this;
    }

    /**
     * @return File|null
     */
    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    /**
     * @param File|null $imageFile
     * @return ContactUs
     */
    public function setImageFile(?File $imageFile): ContactUs
    {
        $this->imageFile = $imageFile;
        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function setImageName(?string $imageName): self
    {
        $this->imageName = $imageName;

        return $this;
    }

    public function getPriority(): ?string
    {
        return $this->priority;
    }

    public function setPriority(string $priority): self
    {
        $this->priority = $priority;

        return $this;
    }

    public function getReplyContactUs(): ?ReplyContactUs
    {
        return $this->replyContactUs;
    }

    public function setReplyContactUs(ReplyContactUs $replyContactUs): self
    {
        // set the owning side of the relation if necessary
        if ($replyContactUs->getContactUs() !== $this) {
            $replyContactUs->setContactUs($this);
        }

        $this->replyContactUs = $replyContactUs;

        return $this;
    }

    /**
     * @return bool
     */
    public function isSeen(): bool
    {
        return $this->isSeen;
    }

    /**
     * @param bool $isSeen
     * @return ContactUs
     */
    public function setIsSeen(bool $isSeen): ContactUs
    {
        $this->isSeen = $isSeen;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     * @return ContactUs
     */
    public function setFirstName($firstName): ContactUs
    {
        $this->firstName = $firstName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     * @return ContactUs
     */
    public function setLastName($lastName): ContactUs
    {
        $this->lastName = $lastName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    public function getTwoLettersName(): string
    {
        return substr($this->getFirstName(), 0, 1) . substr($this->getLastName(), 0, 1);
    }

    /**
     * @return string
     */
    public function getFormattedCreatedAt(): string
    {
        return $this->formattedCreatedAt;
    }

    /**
     * @return ContactUs
     */
    public function setFormattedCreatedAt(): ContactUs
    {
        $this->formattedCreatedAt = date_format($this->getCreatedAt(), 'd M, Y');
        return $this;
    }

    /**
     * @return string
     */
    public function getImgTargetPath(): ?string
    {
        return $this->imgTargetPath;
    }

    /**
     * @param string $imgTargetPath
     * @return ContactUs
     */
    public function setImgTargetPath(string $imgTargetPath): ContactUs
    {
        $this->imgTargetPath = $imgTargetPath;
        return $this;
    }

    public function hasReply(): bool
    {
        return $this->getReplyContactUs() !== null;
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
}
