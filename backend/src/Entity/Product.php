<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ApiFilter(SearchFilter::class, properties: ['title' => 'partial', 'description' => 'partial'])]
#[ApiResource(operations: [
    new Get(),
    new GetCollection(),
    new Post(security: "is_granted('ROLE_ADMIN')"),
    new Patch(security: "is_granted('ROLE_ADMIN')"),
    new Delete(security: "is_granted('ROLE_ADMIN')"),
])]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 30)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 30)]
    private ?string $title = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    private ?string $description = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?string $priceHT = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?string $priceTTC = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isActive = true;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank]
    #[Assert\PositiveOrZero]
    private ?int $quantity = null;

    #[ORM\ManyToOne(targetEntity: Brand::class, inversedBy: 'products')]
    private ?Brand $brand = null;

    /** @var Collection<int, mixed> */
    #[ORM\OneToMany(mappedBy: 'product', targetEntity: Rate::class)]
    private Collection $rate;

    /** @var Collection<int, mixed> */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'products')]
    private Collection $category;

    #[ORM\ManyToOne(targetEntity: CartLine::class, inversedBy: 'products')]
    private ?CartLine $cartLine = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $cover = null;

    public function __construct()
    {
        $this->rate = new ArrayCollection();
        $this->category = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

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

    public function getPriceHT(): ?string
    {
        return $this->priceHT;
    }

    public function setPriceHT(string $priceHT): self
    {
        $this->priceHT = $priceHT;

        return $this;
    }

    public function getPriceTTC(): ?string
    {
        return $this->priceTTC;
    }

    public function setPriceTTC(string $priceTTC): self
    {
        $this->priceTTC = $priceTTC;

        return $this;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getBrand(): ?Brand
    {
        return $this->brand;
    }

    public function setBrand(?Brand $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    /**
     * @return Collection<int, Rate>
     */
    /** @return Collection<int, mixed> */
    public function getRate(): Collection
    {
        return $this->rate;
    }

    public function addRate(Rate $rate): self
    {
        if (!$this->rate->contains($rate)) {
            $this->rate[] = $rate;
            $rate->setProduct($this);
        }

        return $this;
    }

    public function removeRate(Rate $rate): self
    {
        if ($this->rate->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getProduct() === $this) {
                $rate->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Category>
     */
    /** @return Collection<int, mixed> */
    public function getCategory(): Collection
    {
        return $this->category;
    }

    public function addCategory(Category $category): self
    {
        if (!$this->category->contains($category)) {
            $this->category[] = $category;
        }

        return $this;
    }

    public function removeCategory(Category $category): self
    {
        $this->category->removeElement($category);

        return $this;
    }

    public function getCartLine(): ?CartLine
    {
        return $this->cartLine;
    }

    public function setCartLine(?CartLine $cartLine): self
    {
        $this->cartLine = $cartLine;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getCover(): ?string
    {
        return $this->cover;
    }

    public function setCover(?string $cover): self
    {
        $this->cover = $cover;

        return $this;
    }
}
