<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\BillRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BillRepository::class)]
#[ApiResource]
class Bill
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $number;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\Column(type: 'string', length: 255)]
    private $payment;

    #[ORM\OneToOne(mappedBy: 'bill', targetEntity: Order::class, cascade: ['persist', 'remove'])]
    private $command;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getPayment(): ?string
    {
        return $this->payment;
    }

    public function setPayment(string $payment): self
    {
        $this->payment = $payment;

        return $this;
    }

    public function getCommand(): ?Order
    {
        return $this->command;
    }

    public function setCommand(?Order $command): self
    {
        // unset the owning side of the relation if necessary
        if ($command === null && $this->command !== null) {
            $this->command->setBill(null);
        }

        // set the owning side of the relation if necessary
        if ($command !== null && $command->getBill() !== $this) {
            $command->setBill($this);
        }

        $this->command = $command;

        return $this;
    }
}
