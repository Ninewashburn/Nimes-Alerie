<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ApiResource(operations: [
    new Get(),
    new GetCollection(),
    new Post(security: "is_granted('ROLE_ADMIN')"),
    new Patch(security: "is_granted('ROLE_ADMIN')"),
    new Delete(security: "is_granted('ROLE_ADMIN')"),
])]
#[ORM\Entity(repositoryClass: TypeRepository::class)]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: 'text')]
    private ?string $description = null;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: SubType::class, orphanRemoval: true)]
    private Collection $subtype;

    public function __construct()
    {
        $this->subtype = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    /**
     * @return Collection|SubType[]
     */
    public function getSubtypes(): Collection
    {
        return $this->subtype;
    }

    public function addSubtype(SubType $subtypeItem): self
    {
        if (!$this->subtype->contains($subtypeItem)) {
            $this->subtype[] = $subtypeItem;
            $subtypeItem->setType($this);
        }

        return $this;
    }

    public function removeSubtype(SubType $subtypeItem): self
    {
        if ($this->subtype->removeElement($subtypeItem)) {
            // set the owning side to null (unless already changed)
            if ($subtypeItem->getType() === $this) {
                $subtypeItem->setType(null);
            }
        }

        return $this;
    }
}
