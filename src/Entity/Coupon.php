<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\CouponRepository;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Api\Provider\MyCouponsProvider;
use App\Enum\ClassLevel;
use App\Enum\CouponStatus;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[ApiResource(
  operations: [
    // Liste des coupons du parent connecté (Provider)
    new GetCollection(
      provider: MyCouponsProvider::class,
      security: "is_granted('ROLE_PARENT')"
    ),
    // Lecture d’un coupon si c’est son enfant
    new Get(security: "is_granted('ROLE_PARENT') and object.getChild().getParent() == user"),
  ],
  normalizationContext: ['groups'=>['coupon:read']]
)]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'coupons')]
    private ?Child $child = null;

    #[ORM\ManyToOne(inversedBy: 'coupons')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Subject $subject = null;

    // #[ORM\Column(length: 255)]
    // private ?string $classLevel = null;
    #[ORM\Column(enumType: ClassLevel::class)]
    private ?ClassLevel $classLevel = null;

    #[ORM\Column]
    private ?int $durationMinutes = null;

    #[ORM\Column]
    private ?int $remainingMinutes = null;

    // #[ORM\Column(length: 255)]
    // private ?string $status = null;
    #[ORM\Column(enumType: CouponStatus::class)]
    private ?CouponStatus $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $purchasedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastUsedAt = null;

    /**
     * @var Collection<int, CouponUsage>
     */
    #[ORM\OneToMany(targetEntity: CouponUsage::class, mappedBy: 'coupon')]
    private Collection $couponUsages;

    public function __construct()
    {
        $this->couponUsages = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): static
    {
        $this->code = $code;

        return $this;
    }

    public function getChild(): ?Child
    {
        return $this->child;
    }

    public function setChild(?Child $child): static
    {
        $this->child = $child;

        return $this;
    }

    public function getSubject(): ?Subject
    {
        return $this->subject;
    }

    public function setSubject(?Subject $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function getClassLevel(): ?ClassLevel
    {
        return $this->classLevel;
    }

    public function setClassLevel(ClassLevel $classLevel): static
    {
        $this->classLevel = $classLevel;

        return $this;
    }

    public function getDurationMinutes(): ?int
    {
        return $this->durationMinutes;
    }

    public function setDurationMinutes(int $durationMinutes): static
    {
        $this->durationMinutes = $durationMinutes;

        return $this;
    }

    public function getRemainingMinutes(): ?int
    {
        return $this->remainingMinutes;
    }

    public function setRemainingMinutes(int $remainingMinutes): static
    {
        $this->remainingMinutes = $remainingMinutes;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(CouponStatus $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getPurchasedAt(): ?\DateTimeImmutable
    {
        return $this->purchasedAt;
    }

    public function setPurchasedAt(\DateTimeImmutable $purchasedAt): static
    {
        $this->purchasedAt = $purchasedAt;

        return $this;
    }

    public function getLastUsedAt(): ?\DateTimeImmutable
    {
        return $this->lastUsedAt;
    }

    public function setLastUsedAt(?\DateTimeImmutable $lastUsedAt): static
    {
        $this->lastUsedAt = $lastUsedAt;

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
            $couponUsage->setCoupon($this);
        }

        return $this;
    }

    public function removeCouponUsage(CouponUsage $couponUsage): static
    {
        if ($this->couponUsages->removeElement($couponUsage)) {
            // set the owning side to null (unless already changed)
            if ($couponUsage->getCoupon() === $this) {
                $couponUsage->setCoupon(null);
            }
        }

        return $this;
    }
}
