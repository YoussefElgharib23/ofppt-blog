<?php

namespace App\Entity;

use App\Repository\AdminReportRepository;
use App\Traits\TimeStampsTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AdminReportRepository::class)
 * @ORM\Table ("`admin_reports`")
 * @ORM\HasLifecycleCallbacks()
 */
class AdminReport
{
    use TimeStampsTrait;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="adminReports")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $AdminUsername;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $needToReceive = false;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAdminUsername(): ?string
    {
        return $this->AdminUsername;
    }

    public function setAdminUsername(string $AdminUsername): self
    {
        $this->AdminUsername = $AdminUsername;

        return $this;
    }

    public function getNeedToReceive(): ?bool
    {
        return $this->needToReceive;
    }

    public function setNeedToReceive(bool $needToReceive): self
    {
        $this->needToReceive = $needToReceive;

        return $this;
    }
}
