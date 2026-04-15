<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\RateRepository;
use App\State\RateStateProcessor;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RateRepository::class)]
#[ORM\UniqueConstraint(name: 'uq_rate_user_product', columns: ['user_id', 'product_id'])]
#[UniqueEntity(fields: ['user', 'product'], message: 'Vous avez déjà noté ce produit.')]
#[ApiResource(operations: [
    new GetCollection(),
    new Get(),
    new Post(
        security: "is_granted('ROLE_USER')",
        processor: RateStateProcessor::class,
    ),
    new Patch(security: "is_granted('ROLE_ADMIN') or object.getUser() == user"),
    new Delete(security: "is_granted('ROLE_ADMIN') or object.getUser() == user"),
])]
class Rate
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\Range(min: 1, max: 5)]
    private ?int $rate = null;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Assert\Length(max: 2000)]
    private ?string $testimonial = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'rate')]
    private ?Product $product = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'rate')]
    #[ApiProperty(writable: false)]
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
