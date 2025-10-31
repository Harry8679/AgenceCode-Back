<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Repository\TariffRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TariffRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_tariff_combo', columns: ['subject_id','class_level','duration_minutes'])]
#[API\ApiResource(
    operations: [
        new API\GetCollection(),
        new API\Get(),
        new API\Post(security: "is_granted('ROLE_ADMIN')"),
        new API\Patch(security: "is_granted('ROLE_ADMIN')"),
        new API\Delete(security: "is_granted('ROLE_ADMIN')"),
    ],
    normalizationContext: ['groups' => ['tariff:read']],
    denormalizationContext: ['groups' => ['tariff:write']],
)]
class Tariff
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['tariff:read'])]
    private ?int $id = null;

    // tu peux convertir plus tard en enum si tu veux
    #[ORM\Column(length: 255)]
    #[Groups(['tariff:read','tariff:write'])]
    private ?string $classLevel = null;

    #[ORM\ManyToOne(inversedBy: 'tariffs')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['tariff:read','tariff:write'])]
    private ?Subject $subject = null;

    // prix en centimes AVANT crédit d’impôt
    #[ORM\Column(options: ['unsigned' => true])]
    #[Groups(['tariff:read','tariff:write'])]
    private ?int $priceCentsBeforeCredit = null;

    // prix en centimes APRÈS crédit d’impôt
    #[ORM\Column(options: ['unsigned' => true])]
    #[Groups(['tariff:read','tariff:write'])]
    private ?int $priceCentsAfterCredit = null;

    #[ORM\Column(options: ['unsigned' => true])]
    #[Groups(['tariff:read','tariff:write'])]
    private ?int $durationMinutes = null;

    #[ORM\Column]
    #[Groups(['tariff:read','tariff:write'])]
    private bool $isActive = true;

    // ✅ NOUVEAU : ce que tu paies au prof pour ce coupon
    #[ORM\Column(options: ['unsigned' => true])]
    #[Groups(['tariff:read','tariff:write'])]
    private ?int $teacherRateCents = null;


    // --- getters/setters ---

    public function getId(): ?int { return $this->id; }

    public function getClassLevel(): ?string { return $this->classLevel; }
    public function setClassLevel(string $classLevel): static { $this->classLevel = $classLevel; return $this; }

    public function getSubject(): ?Subject { return $this->subject; }
    public function setSubject(?Subject $subject): static { $this->subject = $subject; return $this; }

    public function getPriceCentsBeforeCredit(): ?int { return $this->priceCentsBeforeCredit; }
    public function setPriceCentsBeforeCredit(int $cents): static { $this->priceCentsBeforeCredit = $cents; return $this; }

    public function getPriceCentsAfterCredit(): ?int { return $this->priceCentsAfterCredit; }
    public function setPriceCentsAfterCredit(int $cents): static { $this->priceCentsAfterCredit = $cents; return $this; }

    public function getDurationMinutes(): ?int { return $this->durationMinutes; }
    public function setDurationMinutes(int $minutes): static { $this->durationMinutes = $minutes; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $active): static { $this->isActive = $active; return $this; }

    public function getTeacherRateCents(): ?int { return $this->teacherRateCents; }
    public function setTeacherRateCents(int $cents): self { $this->teacherRateCents = $cents; return $this; }
}
