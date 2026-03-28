<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\CartRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
#[ApiResource]
class Cart
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'datetime')]
    private $createdAt;

    #[ORM\OneToOne(inversedBy: 'cart', targetEntity: Order::class, cascade: ['persist', 'remove'])]
    private $command;

    #[ORM\OneToOne(inversedBy: 'cart', targetEntity: CartLine::class, cascade: ['persist', 'remove'])]
    private $cartLine;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'carts')]
    private $user;

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

    public function getCommand(): ?Order
    {
        return $this->command;
    }

    public function setCommand(?Order $command): self
    {
        $this->command = $command;

        return $this;
    }

    public function getCartLine(): ?cartLine
    {
        return $this->cartLine;
    }

    public function setCartLine(?cartLine $cartLine): self
    {
        $this->cartLine = $cartLine;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
