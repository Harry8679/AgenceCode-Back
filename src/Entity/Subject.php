<?php

namespace App\Entity;

use ApiPlatform\Metadata as API;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\SubjectRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SubjectRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_subject_name', fields: ['name'])]
#[ORM\UniqueConstraint(name: 'uniq_subject_slug', fields: ['slug'])]
#[API\ApiResource(
    normalizationContext: ['groups' => ['subject:read']],
    denormalizationContext: ['groups' => ['subject:write']],
    operations: [
        new API\GetCollection(),                                 // public
        new API\Post(security: "is_granted('ROLE_ADMIN')"),      // admin only
        new API\Get(),                                           // public
        new API\Patch(security: "is_granted('ROLE_ADMIN')"),
        new API\Delete(security: "is_granted('ROLE_ADMIN')"),
    ]
)]
#[API\ApiFilter(SearchFilter::class, properties: ['name' => 'ipartial', 'slug' => 'exact'])]
class Subject
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['subject:read','child:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['subject:read','subject:write','child:read'])]
    private ?string $name = null;

    #[ORM\Column(length: 255)]
    #[Groups(['subject:read','subject:write'])]
    private ?string $slug = null;

    /** @var Collection<int, Child> */
    #[ORM\ManyToMany(targetEntity: Child::class, mappedBy: 'subjects')]
    private Collection $children;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    #[ORM\PrePersist]
    #[ORM\PreUpdate]
    public function ensureSlug(): void
    {
        if (!$this->name) {
            return;
        }
        if (!$this->slug || $this->slug === '') {
            $this->slug = strtolower((new AsciiSlugger())->slug($this->name));
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;
        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): static
    {
        $this->slug = $slug;
        return $this;
    }

    /** @return Collection<int, Child> */
    public function getChildren(): Collection
    {
        return $this->children;
    }

    public function addChild(Child $child): static
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
            $child->addSubject($this);
        }
        return $this;
    }

    public function removeChild(Child $child): static
    {
        if ($this->children->removeElement($child)) {
            $child->removeSubject($this);
        }
        return $this;
    }
}
