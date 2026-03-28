<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\OrderRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
#[ApiResource]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime')]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $status = null;

    #[ORM\OneToOne(targetEntity: OrderLine::class, cascade: ['persist', 'remove'])]
    private ?OrderLine $orderLine = null;

    #[ORM\OneToOne(inversedBy: 'command', targetEntity: Bill::class, cascade: ['persist', 'remove'])]
    private ?Bill $bill = null;

    #[ORM\OneToOne(mappedBy: 'command', targetEntity: Cart::class, cascade: ['persist', 'remove'])]
    private ?Cart $cart = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getOrderLine(): ?OrderLine
    {
        return $this->orderLine;
    }

    public function setOrderLine(?OrderLine $orderLine): self
    {
        $this->orderLine = $orderLine;

        return $this;
    }

    public function getBill(): ?Bill
    {
        return $this->bill;
    }

    public function setBill(?Bill $bill): self
    {
        $this->bill = $bill;

        return $this;
    }

    public function getCart(): ?Cart
    {
        return $this->cart;
    }

    public function setCart(?Cart $cart): self
    {
        // unset the owning side of the relation if necessary
        if ($cart === null && $this->cart !== null) {
            $this->cart->setCommand(null);
        }

        // set the owning side of the relation if necessary
        if ($cart !== null && $cart->getCommand() !== $this) {
            $cart->setCommand($this);
        }

        $this->cart = $cart;

        return $this;
    }
}
