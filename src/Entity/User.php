<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Api\Provider\MyTeachersProvider;
use App\Enum\UserProfile;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\UniqueConstraint(name: 'UNIQ_IDENTIFIER_EMAIL', fields: ['email'])]
#[API\ApiResource(
    // On n’expose AUCUNE route générique sur User,
    // uniquement la collection custom ci-dessous
    operations: [
        new API\GetCollection(
            uriTemplate: '/my/teachers',
            provider: MyTeachersProvider::class,
            normalizationContext: ['groups' => ['teacher:list']],
            security: "is_granted('ROLE_PARENT')"
        ),
    ]
)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    // ========== Colonnes ==========
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['teacher:list'])]
    private ?int $id = null;

    #[ORM\Column(length: 180)]
    #[Groups(['teacher:list'])] // OK de renvoyer l'email d’un prof au parent
    private ?string $email = null;

    /** @var list<string> */
    #[ORM\Column(type: 'json')]
    private array $roles = [];

    /** @var string|null (hash) */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(length: 255)]
    #[Groups(['teacher:list'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['teacher:list'])]
    private ?string $lastName = null;

    #[ORM\Column(options: ['default' => false])]
    private bool $isTaxCreditEligible = false;

    // Profil (Parent / Student / Teacher / Admin)
    #[ORM\Column(type: 'string', enumType: UserProfile::class)]
    private ?UserProfile $profile = null;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['teacher:list'])]
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

    #[ORM\Column(length: 255)]
    #[Groups(['teacher:list'])]
    private ?string $phoneNumber = null;

    // ========== Ctor ==========
    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->roles = [];
        $this->children = new ArrayCollection();
        $this->couponUsages = new ArrayCollection();
    }

    // ========== Getters / Setters ==========
    public function getId(): ?int { return $this->id; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getUserIdentifier(): string { return (string) $this->email; }

    /** @return list<string> */
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

    public function isTaxCreditEligible(): bool { return $this->isTaxCreditEligible; }
    public function setIsTaxCreditEligible(bool $v): self { $this->isTaxCreditEligible = $v; return $this; }

    public function getProfile(): ?UserProfile { return $this->profile; }
    public function setProfile(UserProfile $profile): static
    {
        $this->profile = $profile;
        // roles cohérents avec le profil
        $this->roles = match ($profile) {
            UserProfile::PARENT  => ['ROLE_PARENT'],
            UserProfile::STUDENT => ['ROLE_STUDENT'],
            UserProfile::TEACHER => ['ROLE_TEACHER'],
            UserProfile::ADMIN   => ['ROLE_ADMIN'],
        };
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $createdAt): static { $this->createdAt = $createdAt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static { $this->updatedAt = $updatedAt; return $this; }

    public function getPhoneNumber(): ?string { return $this->phoneNumber; }
    public function setPhoneNumber(string $phoneNumber): static { $this->phoneNumber = $phoneNumber; return $this; }

    // ========== Relations ==========
    /** @return Collection<int, Child> */
    public function getChildren(): Collection { return $this->children; }

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
            if ($child->getParent() === $this) {
                $child->setParent(null);
            }
        }
        return $this;
    }

    /** @return Collection<int, CouponUsage> */
    public function getCouponUsages(): Collection { return $this->couponUsages; }

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
            if ($couponUsage->getTeacher() === $this) {
                $couponUsage->setTeacher(null);
            }
        }
        return $this;
    }

    // ========== Divers sécurité ==========
    public function __serialize(): array
    {
        // Masque le hash du mot de passe si jamais l’objet est sérialisé
        $data = (array) $this;
        $data["\0".self::class."\0password"] = hash('crc32c', (string) $this->password);
        return $data;
    }

    #[\Deprecated]
    public function eraseCredentials(): void {}
}