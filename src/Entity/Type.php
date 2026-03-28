<?php

namespace App\Entity;

use App\Repository\TypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TypeRepository::class)]
class Type
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text')]
    private $description;

    #[ORM\OneToMany(mappedBy: 'type', targetEntity: SubType::class, orphanRemoval: true)]
    private $subtype;

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
