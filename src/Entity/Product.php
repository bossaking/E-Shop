<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=CartPosition::class, mappedBy="Product", orphanRemoval=true)
     */
    private $cartPositions;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $available;


    public function __construct($name, $price, $description, $category)
    {
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->category = $category;
        $this->cartPositions = new ArrayCollection();
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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

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

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|CartPosition[]
     */
    public function getCartPositions(): Collection
    {
        return $this->cartPositions;
    }

    public function addCartPosition(CartPosition $cartPosition): self
    {
        if (!$this->cartPositions->contains($cartPosition)) {
            $this->cartPositions[] = $cartPosition;
            $cartPosition->setProduct($this);
        }

        return $this;
    }

    public function removeCartPosition(CartPosition $cartPosition): self
    {
        if ($this->cartPositions->removeElement($cartPosition)) {
            // set the owning side to null (unless already changed)
            if ($cartPosition->getProduct() === $this) {
                $cartPosition->setProduct(null);
            }
        }

        return $this;
    }

    public function getAvailable(): ?bool
    {
        return $this->available;
    }

    public function setAvailable(?bool $available): self
    {
        $this->available = $available;

        return $this;
    }
}
