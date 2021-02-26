<?php

namespace App\Entity;

use App\Repository\UserRepository;
use App\Traits\TimeStampsTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="users")
 * @UniqueEntity(
 *     fields={"username", "email"},
 *     errorPath="username",
 *     message="The {{ label }} is already taken by another user"
 * )
 * @UniqueEntity(fields="email", message="The {{ label }} is already taken by another user")
 * @ORM\HasLifecycleCallbacks()
 * @Vich\Uploadable()
 */
class User implements UserInterface
{
    use TimeStampsTrait;

    CONST ROLES = [
        'Admin' => 'ROLE_ADMIN',
        'User' => 'ROLE_USER'
    ];

    CONST GENDERS = [
        'Mr.' => 'male',
        'Mme.' => 'female'
    ];

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $username;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="boolean", options={"default": true})
     * @var bool
     */
    private $status;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isVerified = false;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default": null})
     */
    private $displayName;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default": false})
     */
    private $isChangedToDisplay;

    /**
     * @ORM\Column(type="string", length=255, nullable=true, options={"default": null})
     */
    private $phoneNumber;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $birthDate;

    /**
     * @var string
     * @Groups("ajax:posts")
     * @Groups("contact:ajax")
     */
    private $__fullName = '';

    /**
     * @Vich\UploadableField(mapping="users", fileNameProperty="imageName")
     * @Assert\Image(mimeTypes={"image/jpeg", "image/png"})
     * @var File|null
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @var string|null
     */
    private $imageName;

    /**
     * @ORM\OneToMany(targetEntity=Like::class, mappedBy="user", orphanRemoval=true)
     */
    private $likes;

    /**
     * @ORM\OneToMany(targetEntity=Dislike::class, mappedBy="user")
     */
    private $dislikes;

    /**
     * @ORM\OneToMany(targetEntity=Comment::class, mappedBy="user", orphanRemoval=true)
     */
    private $comments;

    /**
     * @ORM\Column(type="string", length=255, options={"default": "male"})
     */
    private $gender;

    /**
     * @ORM\OneToMany(targetEntity=Notification::class, mappedBy="user")
     */
    private $notifications;

    /**
     * @ORM\OneToMany(targetEntity=UserLoginLogs::class, mappedBy="user", orphanRemoval=true)
     */
    private $userLoginLogs;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $isCreatedByAdmin = false;

    /**
     * @ORM\OneToMany(targetEntity=AdminReport::class, mappedBy="user", orphanRemoval=true)
     */
    private $adminReports;

    /**
     * @var string
     * @Groups("ajax:posts")
     */
    private $userImageCache;

    /**
     * @ORM\OneToMany(targetEntity=ReplyContactUs::class, mappedBy="user", orphanRemoval=true)
     */
    private $replyContactUs;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->dislikes = new ArrayCollection();
        $this->notifications = new ArrayCollection();
        $this->userLoginLogs = new ArrayCollection();
        $this->adminReports = new ArrayCollection();
        $this->replyContactUs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     */
    public function setFirstName($firstName): void
    {
        $this->firstName = $firstName;
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
     */
    public function setLastName($lastName): void
    {
        $this->lastName = $lastName;
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
     * @param mixed $username
     */
    public function setUsername($username): void
    {
        $this->username = $username;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->username ?? $this->email;
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
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    /**
     * @param bool|null $showDisplayName
     * @return string
     */
    public function fullName(?bool $showDisplayName = true): string
    {
        if ( $this->getIsChangedToDisplay() and $showDisplayName and $this->getDisplayName() !== null ) return $this->displayName;
        return $this->getFirstName() . ' ' . $this->getLastName();
    }

    /**
     * @return string
     */
    public function firstTowLatterName(): string
    {
        return strtoupper(substr($this->getFirstName(), 0, 1) . substr($this->getLastName(), 0, 1));
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function setDisplayName(?string $displayName): self
    {
        $this->displayName = $displayName;

        return $this;
    }

    public function getIsChangedToDisplay(): ?bool
    {
        return $this->isChangedToDisplay;
    }

    public function setIsChangedToDisplay(bool $isChangedToDisplay): self
    {
        $this->isChangedToDisplay = $isChangedToDisplay;

        return $this;
    }

    public function getPhoneNumber(): ?string
    {
        return $this->phoneNumber;
    }

    public function setPhoneNumber(string $phoneNumber): self
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    public function getBirthDate(): ?string
    {
        return $this->birthDate;
    }

    public function setBirthDate(?string $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
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

    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password,
            'imageName' => $this->imageName
        ];
    }

    public function __unserialize(array $serialized)
    {
        $this->id = $serialized['id'];
        $this->firstName = $serialized['firstName'];
        $this->lastName = $serialized['lastName'];
        $this->username = $serialized['username'];
        $this->email = $serialized['email'];
        $this->password = $serialized['password'];
        $this->imageName = $serialized['imageName'];
    }

    /**
     * @return Collection|Like[]
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): self
    {
        if (!$this->likes->contains($like)) {
            $this->likes[] = $like;
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
     * @return Collection|Dislike[]
     */
    public function getDislikes(): Collection
    {
        return $this->dislikes;
    }

    public function addDislike(Dislike $dislike): self
    {
        if (!$this->dislikes->contains($dislike)) {
            $this->dislikes[] = $dislike;
            $dislike->setUser($this);
        }

        return $this;
    }

    public function removeDislike(Dislike $dislike): self
    {
        if ($this->dislikes->removeElement($dislike)) {
            // set the owning side to null (unless already changed)
            if ($dislike->getUser() === $this) {
                $dislike->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isStatus(): bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return User
     */
    public function setStatus(bool $status): User
    {
        $this->status = $status;
        return $this;
    }


    /**
     * @return Collection|Comment[]
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): self
    {
        if (!$this->comments->contains($comment)) {
            $this->comments[] = $comment;
            $comment->setUser($this);
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

    public function delete()
    {
        $this->setDeletedAt(new \DateTimeImmutable());
    }

    public function recover()
    {
        $this->setDeletedAt(null);
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(string $gender): self
    {
        $this->gender = $gender;

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

    /**
     * @return Collection|UserLoginLogs[]
     */
    public function getUserLoginLogs(): Collection
    {
        return $this->userLoginLogs;
    }

    public function addUserLoginLog(UserLoginLogs $userLoginLog): self
    {
        if (!$this->userLoginLogs->contains($userLoginLog)) {
            $this->userLoginLogs[] = $userLoginLog;
            $userLoginLog->setUser($this);
        }

        return $this;
    }

    public function removeUserLoginLog(UserLoginLogs $userLoginLog): self
    {
        if ($this->userLoginLogs->removeElement($userLoginLog)) {
            // set the owning side to null (unless already changed)
            if ($userLoginLog->getUser() === $this) {
                $userLoginLog->setUser(null);
            }
        }

        return $this;
    }

    public function getIsCreatedByAdmin(): ?bool
    {
        return $this->isCreatedByAdmin;
    }

    public function setIsCreatedByAdmin(bool $isCreatedByAdmin): self
    {
        $this->isCreatedByAdmin = $isCreatedByAdmin;

        return $this;
    }

    /**
     * @return UserLoginLogs|null
     */
    public function getLastLogin(): ?UserLoginLogs
    {
        if ($this->getUserLoginLogs()->count() == 1)
            return $this->getUserLoginLogs()[0];
        else if ($this->getUserLoginLogs()->count() > 1) 
            return $this->getUserLoginLogs()[$this->getUserLoginLogs()->count() - 1];
        return null;
    }

    /**
     * @return Collection|AdminReport[]
     */
    public function getAdminReports(): Collection
    {
        return $this->adminReports;
    }

    /**
     * @return Collection|AdminReport[]
     */
    public function getActiveAdminReports(): array
    {
        $_retArr = new ArrayCollection();
        foreach ($this->getAdminReports() as $report) {
            if (!$report->isDeleted()) $_retArr[]  = $report;
        }
        return array_reverse($_retArr->toArray());
    }


    public function addAdminReport(AdminReport $adminReport): self
    {
        if (!$this->adminReports->contains($adminReport)) {
            $this->adminReports[] = $adminReport;
            $adminReport->setUser($this);
        }

        return $this;
    }

    public function removeAdminReport(AdminReport $adminReport): self
    {
        if ($this->adminReports->removeElement($adminReport)) {
            // set the owning side to null (unless already changed)
            if ($adminReport->getUser() === $this) {
                $adminReport->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getFullName(): string
    {
        return $this->__fullName;
    }

    /**
     * @return User
     */
    public function setFullName(): User
    {
        $this->__fullName = $this->fullName(true);
        return $this;
    }

    /**
     * @return string
     */
    public function getUserImageCache(): ?string
    {
        return $this->userImageCache;
    }

    /**
     * @param string $userImageCache
     * @return User
     */
    public function setUserImageCache(string $userImageCache): User
    {
        $this->userImageCache = $userImageCache;
        return $this;
    }

    /**
     * @return Collection|ReplyContactUs[]
     */
    public function getReplyContactUs(): Collection
    {
        return $this->replyContactUs;
    }

    public function addReplyContactUs(ReplyContactUs $replyContactUs): self
    {
        if (!$this->replyContactUs->contains($replyContactUs)) {
            $this->replyContactUs[] = $replyContactUs;
            $replyContactUs->setUser($this);
        }

        return $this;
    }

    public function removeReplyContactUs(ReplyContactUs $replyContactUs): self
    {
        if ($this->replyContactUs->removeElement($replyContactUs)) {
            // set the owning side to null (unless already changed)
            if ($replyContactUs->getUser() === $this) {
                $replyContactUs->setUser(null);
            }
        }

        return $this;
    }

    public function active()
    {
        $this->setStatus(true);
    }
}