<?php
namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Enum\AssignmentStatus;
use App\Repository\TeacherAssignmentRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: TeacherAssignmentRepository::class)]
#[ORM\UniqueConstraint(
  name: 'uniq_active_link',
  columns: ['teacher_id','child_id','subject_id','is_active']
)]
#[API\ApiResource(
  normalizationContext: ['groups'=>['assign:read']],
  denormalizationContext: ['groups'=>['assign:write']],
  operations: [
    // Parent : déposer une demande (sans prof ciblé)
    new API\Post(
      security: "is_granted('ROLE_PARENT')",
      denormalizationContext: ['groups'=>['assign:create:parent']]
    ),
    // Prof : postuler
    new API\Post(
      uriTemplate: '/teacher_assignments/apply',
      security: "is_granted('ROLE_TEACHER')",
      denormalizationContext: ['groups'=>['assign:apply:teacher']]
    ),
    // Admin : proposer / assigner / mettre à jour le statut
    new API\Patch(security: "is_granted('ROLE_ADMIN')"),
    // Listes filtrées
    new API\GetCollection(
      uriTemplate: '/my/assignments',
      provider: App\Api\Provider\MyAssignmentsProvider::class,
      security: "is_granted('ROLE_USER')"
    ),
    // Lecture détaillée si concerné
    new API\Get(security: "is_granted('ROLE_ADMIN') or object.isActor(user)")
  ]
)]
class TeacherAssignment
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    #[Groups(['assign:read'])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[Groups(['assign:read','assign:write','assign:create:parent','assign:apply:teacher'])]
    private ?User $teacher = null; // peut être null pour REQUESTED

    #[ORM\ManyToOne(targetEntity: Child::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['assign:read','assign:write','assign:create:parent','assign:apply:teacher'])]
    private ?Child $child = null;

    #[ORM\ManyToOne(targetEntity: Subject::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['assign:read','assign:write','assign:create:parent','assign:apply:teacher'])]
    private ?Subject $subject = null;

    #[ORM\Column(enumType: AssignmentStatus::class)]
    #[Groups(['assign:read','assign:write'])]
    private AssignmentStatus $status = AssignmentStatus::REQUESTED;

    #[ORM\Column(type:'boolean', options:['default'=>false])]
    #[Groups(['assign:read'])]
    private bool $isActive = false; // vrai quand ACCEPTED

    #[ORM\Column(type:'string', length:16, nullable:true)]
    #[Groups(['assign:read','assign:write'])]
    private ?string $initiator = null; // 'PARENT'|'TEACHER'|'ADMIN'

    #[ORM\Column(type:'text', nullable:true)]
    #[Groups(['assign:read','assign:write','assign:create:parent','assign:apply:teacher'])]
    private ?string $message = null;

    #[ORM\Column(type:'integer', nullable:true)]
    #[Groups(['assign:read','assign:write'])]
    private ?int $teacherRateCents = null;

    #[ORM\Column(type:'datetime_immutable')]
    #[Groups(['assign:read'])]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(type:'datetime_immutable', nullable:true)]
    #[Groups(['assign:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct() { $this->createdAt = new \DateTimeImmutable(); }

    // … getters/setters …

    // Sécurité lecture item
    public function isActor(User $u): bool {
        if (in_array('ROLE_ADMIN', $u->getRoles(), true)) return true;
        if ($this->teacher && $this->teacher->getId() === $u->getId()) return true;
        return $this->child && $this->child->getParent()?->getId() === $u->getId();
    }

    public function accept(): void {
        $this->status = AssignmentStatus::ACCEPTED;
        $this->isActive = true;
    }
    public function decline(): void {
        $this->status = AssignmentStatus::DECLINED;
        $this->isActive = false;
    }
}