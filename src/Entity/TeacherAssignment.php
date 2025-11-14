<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

// 👉 imports manquants
use App\Enum\AssignmentStatus;
use App\Api\Provider\MyAssignmentsProvider;
use App\Entity\User;
use App\Entity\Child;
use App\Entity\Subject;

#[ORM\Entity]
#[API\ApiResource(
    operations: [
        new API\Post(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_PARENT')"
        ),
        new API\Patch(
            security: "is_granted('ROLE_ADMIN')"
        ),
        // Liste filtrée (exemple)
        new API\GetCollection(
            uriTemplate: '/my/assignments',
            provider: MyAssignmentsProvider::class, // 👈 résolu via use
            security: "is_granted('ROLE_USER')"
        ),
        new API\Get(
            security: "is_granted('ROLE_ADMIN') or object.isActor(user)"
        ),
    ]
)]
class TeacherAssignment
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['assign:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['assign:read','assign:write','assign:create:parent','assign:apply:teacher'])]
    private ?User $teacher = null; // peut rester null pour REQUESTED

    #[ORM\ManyToOne(targetEntity: Child::class)]
    #[Groups(['assign:read','assign:write','assign:create:parent'])]
    private ?Child $child = null;

    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[Groups(['assign:read','assign:write','assign:create:parent','assign:apply:teacher'])]
    private ?Subject $subject = null;

    #[ORM\Column(enumType: AssignmentStatus::class)]
    #[Groups(['assign:read'])]
    private AssignmentStatus $status = AssignmentStatus::REQUESTED;

    #[ORM\Column(options: ['default' => false])]
    #[Groups(['assign:read'])]
    private bool $isActive = false;

    #[ORM\Column]
    #[Groups(['assign:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    #[Groups(['assign:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    // --- helpers de sécurité (exemple) ---
    public function isActor(User $u): bool
    {
        if ($this->teacher && $this->teacher->getId() === $u->getId()) return true;
        if ($this->child && $this->child->getParent() && $this->child->getParent()->getId() === $u->getId()) return true;
        return false;
    }

    // --- transitions de statut ---
    public function accept(): void
    {
        $this->status = AssignmentStatus::ACCEPTED;
        $this->isActive = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function decline(): void
    {
        $this->status = AssignmentStatus::DECLINED;
        $this->isActive = false;
        $this->updatedAt = new \DateTimeImmutable();
    }

    // --- getters / setters (exemples essentiels) ---
    public function getId(): ?int { return $this->id; }

    public function getTeacher(): ?User { return $this->teacher; }
    public function setTeacher(?User $teacher): self { $this->teacher = $teacher; return $this; }

    public function getChild(): ?Child { return $this->child; }
    public function setChild(?Child $child): self { $this->child = $child; return $this; }

    public function getSubject(): ?Subject { return $this->subject; }
    public function setSubject(?Subject $subject): self { $this->subject = $subject; return $this; }

    public function getStatus(): AssignmentStatus { return $this->status; }
    public function setStatus(AssignmentStatus $status): self { $this->status = $status; return $this; }

    public function isActive(): bool { return $this->isActive; }
    public function setIsActive(bool $active): self { $this->isActive = $active; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function setCreatedAt(\DateTimeImmutable $dt): self { $this->createdAt = $dt; return $this; }

    public function getUpdatedAt(): ?\DateTimeImmutable { return $this->updatedAt; }
    public function setUpdatedAt(?\DateTimeImmutable $dt): self { $this->updatedAt = $dt; return $this; }
}
