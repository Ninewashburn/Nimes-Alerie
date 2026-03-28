<?php

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
    private $id;

    #[ORM\Column(type: 'integer', nullable: true)]
    private $rate;

    #[ORM\Column(type: 'text', nullable: true)]
    private $testimonial;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'rate')]
    private $product;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'Rate')]
    private $user;

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
