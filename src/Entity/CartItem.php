<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;
use Ramsey\Uuid\Doctrine\UuidGenerator;

/**
 * Class CartItem
 * @package App\Entity
 * @ORM\Entity
 */
class CartItem
{
    /**
     * @ORM\Id
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @var int
     * @ORM\Column(type="integer")
     */
    private int $quantity = 0;

    /**
     * @var Product
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Product $product;

    /**
     * @var Customer|null
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer", inversedBy="cart")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private ?Customer $customer = null;

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return CartItem
     */
    public function setCustomer(Customer $customer): CartItem
    {
        $this->customer = $customer;
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
     * @return CartItem
     */
    public function setQuantity(int $quantity): CartItem
    {
        $this->quantity = $quantity;
        if ($this->quantity <= 0) {
            $this->customer->getCart()->removeElement($this);
            $this->customer = null;
        }
        return $this;
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return CartItem
     */
    public function setProduct(Product $product): CartItem
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

    public function getPriceIncludingTaxes(): float
    {
        return $this->product->getPriceIncludingTaxes() * $this->quantity;
    }

    public function getTotalAmountTaxes(): float
    {
        return $this->product->getTaxesAmount() * $this->quantity;
    }

    public function getTotalAmountWithoutTaxes(): float
    {
        return ($this->product->getPrice()->getUnitPrice() / 100) * $this->quantity;
    }
}
