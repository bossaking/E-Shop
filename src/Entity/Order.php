<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 */
class Order
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=CartPosition::class, mappedBy="cartOrder")
     */
    private $CartPositions;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $User;

    /**
     * @ORM\ManyToOne(targetEntity=Status::class, inversedBy="Orders")
     * @ORM\JoinColumn(nullable=false)
     */
    private $status;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $Archived;

    public function __construct()
    {
        $this->CartPositions = new ArrayCollection();
        $this->setArchived(false);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|CartPosition[]
     */
    public function getCartPositions(): Collection
    {
        return $this->CartPositions;
    }

    public function addCartPosition(CartPosition $cartPosition): self
    {
        if (!$this->CartPositions->contains($cartPosition)) {
            $this->CartPositions[] = $cartPosition;
            $cartPosition->setCartOrder($this);
        }

        return $this;
    }

    public function removeCartPosition(CartPosition $cartPosition): self
    {
        if ($this->CartPositions->removeElement($cartPosition)) {
            // set the owning side to null (unless already changed)
            if ($cartPosition->getCartOrder() === $this) {
                $cartPosition->setCartOrder(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): self
    {
        $this->User = $User;

        return $this;
    }

    public function getStatus(): ?Status
    {
        return $this->status;
    }

    public function setStatus(?Status $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getArchived(): ?bool
    {
        return $this->Archived;
    }

    public function setArchived(?bool $Archived): self
    {
        $this->Archived = $Archived;

        return $this;
    }
}
