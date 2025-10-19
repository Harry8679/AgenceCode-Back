<?php

namespace App\Entity;

use App\Enum\UserProfile;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    private ?string $email = null;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /** @var string|null (hash) */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    // ✅ on mappe bien l'enum
    #[ORM\Column(type: 'string', enumType: UserProfile::class)]
    private ?UserProfile $profile = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $updatedAt = null;

    /**
     * @var Collection<int, Child>
     */
    #[ORM\OneToMany(targetEntity: Child::class, mappedBy: 'parent')]
    private Collection $children;

    /**
     * @var Collection<int, CouponUsage>
     */
    #[ORM\OneToMany(targetEntity: CouponUsage::class, mappedBy: 'teacher')]
    private Collection $couponUsages;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable(); // ✅ évite le NOT NULL
        $this->roles = [];                           // par défaut
        $this->children = new ArrayCollection();
        $this->couponUsages = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    public function getRoles(): array
    {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    /** @param list<string> $roles */
    public function setRoles(array $roles): static { $this->roles = $roles; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(string $firstName): static { $this->firstName = $firstName; return $this; }

    public function getLastName(): ?string { return $this->lastName; }
    public function setLastName(string $lastName): static { $this->lastName = $lastName; return $this; }

    // ✅ cohérence des types avec l'enum
    public function getProfile(): ?UserProfile { return $this->profile; }
    public function setProfile(UserProfile $profile): static
    {
        $this->profile = $profile;
        $this->roles = match ($profile) {
            UserProfile::PARENT  => ['ROLE_PARENT'],
            UserProfile::STUDENT => ['ROLE_STUDENT'],
            UserProfile::TEACHER => ['ROLE_TEACHER'],
            UserProfile::ADMIN   => ['ROLE_ADMIN'], // utilisé seulement via fixtures/commande
        };
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function __serialize(): array
    {
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', (string) $this->password);
        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void {}

    /**
     * @return Collection<int, Child>
     */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Child $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->setParent($this);
        }

        return $this;
    }

    public function removeChild(Child $child): static
    {
        if ($this->children->removeElement($child)) {
            // set the owning side to null (unless already changed)
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, CouponUsage>
     */
    public function getCouponUsages(): Collection
    {
        return $this->couponUsages;
    }

    public function addCouponUsage(CouponUsage $couponUsage): static
    {
        if (!$this->couponUsages->contains($couponUsage)) {
            $this->couponUsages->add($couponUsage);
            $couponUsage->setTeacher($this);
        }

        return $this;
    }

    public function removeCouponUsage(CouponUsage $couponUsage): static
    {
        if ($this->couponUsages->removeElement($couponUsage)) {
            // set the owning side to null (unless already changed)
            if ($couponUsage->getTeacher() === $this) {
                $couponUsage->setTeacher(null);
            }
        }

        return $this;
    }
}
