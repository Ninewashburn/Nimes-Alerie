<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\RateRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RateRepository::class)]
#[ApiResource]
class Rate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $rate = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $testimonial = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'rate')]
    private ?Product $product = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'Rate')]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(?int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getTestimonial(): ?string
    {
        return $this->testimonial;
    }

    public function setTestimonial(?string $testimonial): self
    {
        $this->testimonial = $testimonial;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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
