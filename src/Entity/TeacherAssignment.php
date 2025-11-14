<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Api\Provider\MyAssignmentsProvider;
use App\Enum\AssignmentStatus;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[API\ApiResource(
    normalizationContext: ['groups' => ['assign:read']],
    denormalizationContext: ['groups' => ['assign:write']],
    operations: [
        // collection GET filtrée par user courant (provider)
        new API\GetCollection(
            provider: MyAssignmentsProvider::class,
            security: "is_granted('ROLE_USER')"
        ),
        // lecture item si concerné
        new API\Get(
            security: "is_granted('ROLE_USER') and object.isActor(user)"
        ),
        // création: un parent peut créer une demande (teacher null possible)
        new API\Post(
            security: "is_granted('ROLE_PARENT')"
        ),
        // mise à jour: admin/teacher pourront patcher le statut
        new API\Patch(
            security: "is_granted('ROLE_ADMIN') or is_granted('ROLE_TEACHER')"
        ),
        new API\Delete(
            security: "is_granted('ROLE_ADMIN')"
        ),
    ]
)]
class TeacherAssignment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['assign:read'])]
    private ?int $id = null;

    // Prof lié (peut être null sur REQUESTED)
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['assign:read','assign:write'])]
    private ?User $teacher = null;

    // Enfant concerné
    #[ORM\ManyToOne(targetEntity: Child::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['assign:read','assign:write'])]
    private ?Child $child = null;

    // Matière souhaitée
    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['assign:read','assign:write'])]
    private ?Subject $subject = null;

    // Statut via enum
    #[ORM\Column(enumType: AssignmentStatus::class)]
    #[Groups(['assign:read'])]
    private AssignmentStatus $status = AssignmentStatus::REQUESTED;

    #[ORM\Column]
    #[Groups(['assign:read'])]
    private bool $isActive = true;

    #[ORM\Column(type: 'datetime_immutable')]
    #[Groups(['assign:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    #[Groups(['assign:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->isActive  = true;
        $this->status    = AssignmentStatus::REQUESTED;
    }

    // ----- droits lecture item -----
    public function isActor(User $u): bool
    {
        if ($this->teacher && $this->teacher->getId() === $u->getId()) return true;
        if ($this->child && $this->child->getParent() && $this->child->getParent()->getId() === $u->getId()) return true;
        return in_array('ROLE_ADMIN', $u->getRoles(), true);
    }

    // ----- transitions "rapides" -----
    public function accept(): void
    {
        $this->status   = AssignmentStatus::ACCEPTED;
        $this->isActive = true;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function decline(): void
    {
        $this->status   = AssignmentStatus::DECLINED;
        $this->isActive = false;
        $this->updatedAt = new \DateTimeImmutable();
    }

    // ----- getters / setters -----
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