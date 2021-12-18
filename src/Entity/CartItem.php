<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity]
class CartItem
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity = 0;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Product $product = null;

    #[ORM\ManyToOne(targetEntity: Customer::class, inversedBy: 'cart')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Customer $customer = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function setCustomer(?Customer $customer): CartItem
    {
        $this->customer = $customer;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): CartItem
    {
        $this->quantity = $quantity;
        if ($this->quantity <= 0) {
            $this->customer->getCart()->removeElement($this);
            $this->customer = null;
        }
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): CartItem
    {
        $this->product = $product;
        return $this;
    }

    public function increaseQuantity(): void
    {
        $this->quantity++;
    }

    public function decreaseQuantity(): void
    {
        $this->quantity--;
    }

    #[Pure]
    public function getPriceIncludingTaxes(): float
    {
        if ($this->product) {
            return $this->product->getPriceIncludingTaxes() * $this->quantity;
        }
        return 0.00;
    }

    #[Pure]
    public function getTotalAmountTaxes(): float
    {
        if ($this->product) {
            return $this->product->getTaxesAmount() * $this->quantity;
        }
        return 0.00;
    }

    #[Pure]
    public function getTotalAmountWithoutTaxes(): float
    {
        if ($this->product && $this->product->getPrice()) {
            return ($this->product->getPrice()->getUnitPrice() / 100) * $this->quantity;
        }
        return 0.00;
    }
}
