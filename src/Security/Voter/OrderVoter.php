<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Customer;
use App\Entity\Order;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrderVoter extends Voter
{
    public const CANCEL = 'cancel';

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Order && $attribute === self::CANCEL;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof Customer) {
            return false;
        }

        /** @var Order $subject */

        return $subject->getState() === 'created';
    }
}
