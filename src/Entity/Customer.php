<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Class Customer
 * @package App\Entity
 * @ORM\Entity
 */
class Customer extends User
{
    public const ROLE = 'customer';

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\CartItem", mappedBy="customer", cascade={"persist"}, orphanRemoval=true)
     */
    private Collection $cart;

    public function __construct()
    {
        parent::__construct();
        $this->cart = new ArrayCollection();
    }

    public function getRoles(): array
    {
        return ['ROLE_CUSTOMER'];
    }

    /**
     * @return Collection
     */
    public function getCart()
    {
        return $this->cart;
    }

    public function addToCart(Product $product): void
    {
        $products = $this->cart->filter(fn (CartItem $cartItem) => $cartItem->getProduct() === $product);
        if ($products->count() === 0) {
            $cartItem = (new CartItem())
                ->setId(Uuid::v4())
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
        return array_sum($this->cart->map(fn (CartItem $cartItem) => $cartItem->getPriceIncludingTaxes())->toArray());
    }
}
