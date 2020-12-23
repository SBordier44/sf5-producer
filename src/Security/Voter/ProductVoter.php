<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\CartItem;
use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductVoter extends Voter
{
    public const UPDATE = 'update';
    public const DELETE = 'delete';
    public const ADD_TO_CART = 'add_to_cart';

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Product
            && in_array($attribute, [self::UPDATE, self::DELETE, self::ADD_TO_CART]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $user */
        $user = $token->getUser();

        /** @var Product $subject */

        if ($attribute === self::ADD_TO_CART) {
            return $user instanceof Customer && $this->voteOnAddToCart($user, $subject);
        }

        return $subject->getFarm() === $user->getFarm();
    }

    private function voteOnAddToCart(Customer $customer, Product $product): bool
    {
        if ($customer->getCart()->count() === 0) {
            return true;
        }

        return $customer->getCart()->map(
            fn(CartItem $cartItem) => $cartItem->getProduct()->getFarm()
        )->contains(
            $product->getFarm()
        );
    }
}
