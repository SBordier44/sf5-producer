<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class OrderLine
 * @package App\Entity
 * @ORM\Entity
 */
class OrderLine
{
    /**
     * @var Uuid
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private Uuid $id;

    /**
     * @var Order
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="lines")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Order $order;

    /**
     * @ORM\Embedded(class="Price")
     * @Assert\Valid
     */
    private Price $price;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private int $quantity = 0;

    /**
     * @var Product|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private ?Product $product = null;

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @param Uuid $id
     * @return OrderLine
     */
    public function setId(Uuid $id): OrderLine
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @return OrderLine
     */
    public function setOrder(Order $order): OrderLine
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * @param Price $price
     * @return OrderLine
     */
    public function setPrice(Price $price): OrderLine
    {
        $this->price = $price;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return OrderLine
     */
    public function setQuantity(int $quantity): OrderLine
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product|null $product
     * @return OrderLine
     */
    public function setProduct(?Product $product): OrderLine
    {
        $this->product = $product;
        return $this;
    }

    public function getTotalIncludingTaxes(): float
    {
        return (($this->price->getUnitPrice() * $this->price->getVat()) / 100) * $this->quantity;
    }
}
