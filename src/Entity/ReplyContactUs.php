<?php

namespace App\Entity;

use App\Repository\ReplyContactUsRepository;
use App\Traits\TimeStampsTrait;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=ReplyContactUsRepository::class)
 * @ORM\HasLifecycleCallbacks()
 */
class ReplyContactUs
{
    use TimeStampsTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("contact:ajax")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=ContactUs::class, inversedBy="replyContactUs", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $contactUs;

    /**
     * @ORM\Column(type="text")
     * @Groups("contact:ajax")
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="replyContactUs")
     * @ORM\JoinColumn(nullable=false)
     * @Groups("contact:ajax")
     */
    private $user;

    /**
     * @Groups("contact:ajax")
     * @var string
     */
    private $twoLatter;

    /**
     * @Groups("contact:ajax")
     * @var string
     */
    private $formattedCreatedAt;

    /**
     * @var string
     * @Groups("contact:ajax")
     */
    private $minCreatedAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContactUs(): ?ContactUs
    {
        return $this->contactUs;
    }

    public function setContactUs(ContactUs $contactUs): self
    {
        $this->contactUs = $contactUs;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

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

    /**
     * @return string
     */
    public function getTwoLatter(): ?string
    {
        return $this->twoLatter;
    }

    /**
     * @return ReplyContactUs
     */
    public function setTwoLatter(): ReplyContactUs
    {
        $this->twoLatter = $this->getUser()->firstTowLatterName();
        return $this;
    }

    /**
     * @return string
     */
    public function getFormattedCreatedAt(): string
    {
        return $this->formattedCreatedAt;
    }

    /**
     * @return ReplyContactUs
     */
    public function setFormattedCreatedAt(): ReplyContactUs
    {
        $this->formattedCreatedAt = date_format($this->getCreatedAt() ,'d M Y h:i A');
        return $this;
    }

    /**
     * @return string
     */
    public function getMinCreatedAt(): string
    {
        return $this->minCreatedAt;
    }

    /**
     * @return ReplyContactUs
     */
    public function setMinCreatedAt(): ReplyContactUs
    {
        $this->minCreatedAt = date_format($this->getCreatedAt(), 'd M, Y');
        return $this;
    }
}
