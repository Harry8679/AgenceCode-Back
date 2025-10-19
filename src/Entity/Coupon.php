<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CouponRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CouponRepository::class)]
#[ApiResource]
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

    #[ORM\Column(length: 255)]
    private ?string $classLevel = null;

    #[ORM\Column]
    private ?int $durationMinutes = null;

    #[ORM\Column]
    private ?int $remainingMinutes = null;

    #[ORM\Column(length: 255)]
    private ?string $status = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $purchasedAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $lastUsedAt = null;

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

    public function getClassLevel(): ?string
    {
        return $this->classLevel;
    }

    public function setClassLevel(string $classLevel): static
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

    public function setStatus(string $status): static
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
}
