<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use App\Enum\ClassLevel;
use App\Repository\ChildRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use App\Api\Provider\MyChildrenProvider;
use App\Api\Processor\ChildOwnerProcessor;

#[ORM\Entity(repositoryClass: ChildRepository::class)]
#[API\ApiResource(
    normalizationContext: ['groups' => ['child:read']],
    denormalizationContext: ['groups' => ['child:write']],
    operations: [
        // collection GET => via provider pour filtrer par parent connecté
        new API\GetCollection(
            provider: MyChildrenProvider::class,
            security: "is_granted('ROLE_PARENT')"
        ),
        // POST => parent only, on attribue le parent connecté dans un Processor
        new API\Post(
            security: "is_granted('ROLE_PARENT')",
            processor: ChildOwnerProcessor::class
        ),
        // item GET => autorisé si owner
        new API\Get(security: "is_granted('ROLE_PARENT') and object.getParent() == user"),
        new API\Patch(security: "is_granted('ROLE_PARENT') and object.getParent() == user"),
        new API\Delete(security: "is_granted('ROLE_PARENT') and object.getParent() == user"),
    ]
)]
class Child
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['child:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['child:read','child:write'])]
    private ?string $firstName = null;

    #[ORM\Column(length: 255)]
    #[Groups(['child:read','child:write'])]
    private ?string $lastName = null;

    #[ORM\Column(enumType: ClassLevel::class)]
    #[Groups(['child:read','child:write'])]
    private ?ClassLevel $classLevel = null;

    #[ORM\Column]
    #[Groups(['child:read'])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['child:read'])]
    private ?\DateTimeImmutable $updatedAt = null;

    #[ORM\ManyToOne(inversedBy: 'children')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $parent = null; // pas exposé en write

    /** @var Collection<int, Subject> */
    #[ORM\ManyToMany(targetEntity: Subject::class, inversedBy: 'children')]
    #[ORM\JoinTable(name: 'child_subject')]
    #[Groups(['child:read','child:write'])]   // on poste des IDs de Subject
    private Collection $subjects;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->subjects  = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
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

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function getParent(): ?User
    {
        return $this->parent;
    }

    public function setParent(?User $parent): static
    {
        $this->parent = $parent;
        return $this;
    }

    /** @return Collection<int, Subject> */
    public function getSubjects(): Collection
    {
        return $this->subjects;
    }

    public function addSubject(Subject $subject): static
    {
        if (!$this->subjects->contains($subject)) {
            $this->subjects->add($subject);
        }
        return $this;
    }

    public function removeSubject(Subject $subject): static
    {
        $this->subjects->removeElement($subject);
        return $this;
    }
}
