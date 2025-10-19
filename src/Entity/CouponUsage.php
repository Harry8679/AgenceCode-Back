<?php

namespace App\Entity;

use App\Repository\CouponUsageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponUsageRepository::class)]
class CouponUsage
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'couponUsages')]
    private ?Coupon $coupon = null;

    #[ORM\ManyToOne(inversedBy: 'couponUsages')]
    private ?User $teacher = null;

    #[ORM\Column]
    private ?int $minutesUsed = null;

    #[ORM\Column(type: Types::DATE_IMMUTABLE)]
    private ?\DateTimeImmutable $lessonDate = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCoupon(): ?Coupon
    {
        return $this->coupon;
    }

    public function setCoupon(?Coupon $coupon): static
    {
        $this->coupon = $coupon;

        return $this;
    }

    public function getTeacher(): ?User
    {
        return $this->teacher;
    }

    public function setTeacher(?User $teacher): static
    {
        $this->teacher = $teacher;

        return $this;
    }

    public function getMinutesUsed(): ?int
    {
        return $this->minutesUsed;
    }

    public function setMinutesUsed(int $minutesUsed): static
    {
        $this->minutesUsed = $minutesUsed;

        return $this;
    }

    public function getLessonDate(): ?\DateTimeImmutable
    {
        return $this->lessonDate;
    }

    public function setLessonDate(\DateTimeImmutable $lessonDate): static
    {
        $this->lessonDate = $lessonDate;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
