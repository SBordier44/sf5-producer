<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CustomerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomerRepository::class)]
class Customer extends User
{
    public const ROLE = 'customer';

    #[ORM\OneToMany(mappedBy: 'customer', targetEntity: CartItem::class, cascade: ['persist'], orphanRemoval: true)]
    private Collection $cart;

    public function __construct()
    {
        parent::__construct();
        $this->cart = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return ['ROLE_CUSTOMER', 'ROLE_USER'];
    }

    public function getCart(): Collection
    {
        return $this->cart;
    }

    public function addToCart(Product $product): void
    {
        $products = $this->cart->filter(fn(CartItem $cartItem) => $cartItem->getProduct() === $product);
        if ($products->count() === 0) {
            $cartItem = (new CartItem())
                ->setQuantity(1)
                ->setProduct($product)
                ->setCustomer($this);
            $this->cart->add($cartItem);
            return;
        }
        $products->first()->increaseQuantity();
    }

    public function getTotalCartIncludingTaxes(): float
    {
        return array_sum($this->cart->map(fn(CartItem $cartItem) => $cartItem->getPriceIncludingTaxes())->toArray());
    }

    public function getTotalCartVat(): float
    {
        return array_sum($this->cart->map(fn(CartItem $cartItem) => $cartItem->getTotalAmountTaxes())->toArray());
    }

    public function getTotalCartWithoutTaxes(): float
    {
        return array_sum(
            $this->cart->map(fn(CartItem $cartItem) => $cartItem->getTotalAmountWithoutTaxes())->toArray()
        );
    }
}
