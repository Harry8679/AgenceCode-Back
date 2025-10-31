<?php

namespace App\Entity;

use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Api\Provider\MyCouponsProvider;
use App\Enum\ClassLevel;
use App\Enum\CouponStatus;
use App\Repository\CouponRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(
            provider: MyCouponsProvider::class,
            security: "is_granted('ROLE_PARENT')"
        ),
        new Get(security: "is_granted('ROLE_PARENT') and object.getChild().getParent() == user"),
    ],
    normalizationContext: ['groups' => ['coupon:read']]
)]
class Coupon
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['coupon:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 16)]
    #[Groups(['coupon:read'])]
    private ?string $code = null;

    #[ORM\ManyToOne(inversedBy: 'coupons')]
    #[Groups(['coupon:read'])]
    private ?Child $child = null;            // will serialize as IRI unless Child has groups too

    #[ORM\ManyToOne(inversedBy: 'coupons')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['coupon:read'])]
    private ?Subject $subject = null;        // IRI unless Subject has groups too

    #[ORM\Column(enumType: ClassLevel::class)]
    #[Groups(['coupon:read'])]
    private ?ClassLevel $classLevel = null;

    #[ORM\Column]
    #[Groups(['coupon:read'])]
    private ?int $durationMinutes = null;

    #[ORM\Column]
    #[Groups(['coupon:read'])]
    private ?int $remainingMinutes = null;

    #[ORM\Column(enumType: CouponStatus::class)]
    #[Groups(['coupon:read'])]
    private ?CouponStatus $status = null;

    #[ORM\Column]
    #[Groups(['coupon:read'])]
    private ?\DateTimeImmutable $purchasedAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['coupon:read'])]
    private ?\DateTimeImmutable $lastUsedAt = null;

    /** @var Collection<int, CouponUsage> */
    #[ORM\OneToMany(targetEntity: CouponUsage::class, mappedBy: 'coupon')]
    private Collection $couponUsages;

    // snapshots de prix
    #[ORM\Column(options: ['unsigned' => true])]
    #[Groups(['coupon:read'])]
    private int $unitPriceParentCents = 0;

    #[ORM\Column(options: ['unsigned' => true])]
    #[Groups(['coupon:read'])]
    private int $unitPriceTeacherCents = 0;

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

    public function getStatus(): ?CouponStatus
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

    /** @return Collection<int, CouponUsage> */
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
            if ($couponUsage->getCoupon() === $this) {
                $couponUsage->setCoupon(null);
            }
        }
        return $this;
    }

    public function getUnitPriceParentCents(): int { return $this->unitPriceParentCents; }
    public function setUnitPriceParentCents(int $c): self { $this->unitPriceParentCents = $c; return $this; }

    public function getUnitPriceTeacherCents(): int { return $this->unitPriceTeacherCents; }
    public function setUnitPriceTeacherCents(int $c): self { $this->unitPriceTeacherCents = $c; return $this; }
}