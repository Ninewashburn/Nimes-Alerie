<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\UserRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ApiResource(
    operations: [
        new Get(security: "is_granted('ROLE_ADMIN') or object == user"),
        new GetCollection(security: "is_granted('ROLE_ADMIN')"),
    ],
)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\Email(message: 'The email {{ value }} is not a valid email.', )]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    /** @var array<string> */
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    #[ApiProperty(readable: false, writable: false)]
    private ?string $password = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank, Assert\Length(max: 50)]
    private ?string $firstName = null;

    #[ORM\Column(type: 'string', length: 100)]
    #[Assert\NotBlank, Assert\Length(max: 50)]
    private ?string $lastName = null;

    #[ORM\Column(type: 'string', length: 50, nullable: true)]
    #[Assert\Regex(
        pattern: '/^\+?[0-9\s\-\(\)\.]{6,20}$/',
        message: 'Le numéro de téléphone n\'est pas valide. Formats acceptés : +33 6 12 34 56 78, 0612345678…',
    )]
    private ?string $telephone = null;

    #[ORM\Column(type: 'date')]
    #[Assert\NotBlank]
    private ?DateTimeInterface $birthAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Rate::class)]
    private Collection $rate;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Cart::class)]
    private Collection $carts;

    #[ORM\Column(type: 'string', length: 255)]
    private ?string $address = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $secondAddress = null;

    #[ORM\Column(type: 'string', length: 100)]
    private ?string $city = null;

    #[ORM\Column(type: 'string', length: 100, nullable: true)]
    private ?string $country = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(type: 'string', length: 10, nullable: true)]
    private ?string $gender = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isVerified = false;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    #[ApiProperty(readable: false, writable: false)]
    private ?string $emailVerifyToken = null;

    public function __construct()
    {
        $this->rate = new ArrayCollection();
        $this->carts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    /** @param array<string> $roles */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getBirthAt(): ?DateTimeInterface
    {
        return $this->birthAt;
    }

    public function setBirthAt(DateTimeInterface $birthAt): self
    {
        $this->birthAt = $birthAt;

        return $this;
    }

    /**
     * @return Collection|Rate[]
     */
    public function getRate(): Collection
    {
        return $this->rate;
    }

    public function addRate(Rate $rate): self
    {
        if (!$this->rate->contains($rate)) {
            $this->rate[] = $rate;
            $rate->setUser($this);
        }

        return $this;
    }

    public function removeRate(Rate $rate): self
    {
        if ($this->rate->removeElement($rate)) {
            // set the owning side to null (unless already changed)
            if ($rate->getUser() === $this) {
                $rate->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Cart[]
     */
    public function getCarts(): Collection
    {
        return $this->carts;
    }

    public function addCart(Cart $cart): self
    {
        if (!$this->carts->contains($cart)) {
            $this->carts[] = $cart;
            $cart->setUser($this);
        }

        return $this;
    }

    public function removeCart(Cart $cart): self
    {
        if ($this->carts->removeElement($cart)) {
            // set the owning side to null (unless already changed)
            if ($cart->getUser() === $this) {
                $cart->setUser(null);
            }
        }

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getSecondAddress(): ?string
    {
        return $this->secondAddress;
    }

    public function setSecondAddress(?string $secondAddress): self
    {
        $this->secondAddress = $secondAddress;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function setPostalCode(?string $postalCode): self
    {
        $this->postalCode = $postalCode;

        return $this;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function setGender(?string $gender): self
    {
        $this->gender = $gender;

        return $this;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
    }

    public function getEmailVerifyToken(): ?string
    {
        return $this->emailVerifyToken;
    }

    public function setEmailVerifyToken(?string $emailVerifyToken): self
    {
        $this->emailVerifyToken = $emailVerifyToken;

        return $this;
    }
}
