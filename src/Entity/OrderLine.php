<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
class OrderLine
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id;

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'lines')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Order $order = null;

    #[ORM\Embedded(class: Price::class)]
    private ?Price $price = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity = 0;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    private ?Product $product = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): OrderLine
    {
        $this->order = $order;
        return $this;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): OrderLine
    {
        $this->price = $price;
        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): OrderLine
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): OrderLine
    {
        $this->product = $product;
        return $this;
    }

    #[Pure]
    public function getTotalIncludingTaxes(): float
    {
        $unitPrice = $this->price->getUnitPrice() / 100;
        $vat = $unitPrice * $this->price->getVat() / 100;
        return $unitPrice + $vat * $this->quantity;
    }

    #[Pure]
    public function getTotal(): float
    {
        $unitPrice = $this->price->getUnitPrice() / 100;
        return $unitPrice * $this->quantity;
    }

    #[Pure]
    public function getTaxesAmount(): float
    {
        $unitPrice = $this->price->getUnitPrice() / 100;
        return $unitPrice * $this->price->getVat() / 100;
    }
}
