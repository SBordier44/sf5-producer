<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Producer;
use App\Entity\Product;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class ProductVoter extends Voter
{
    public const UPDATE = 'update';
    public const DELETE = 'delete';

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Product && in_array($attribute, [self::UPDATE, self::DELETE]);
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof Producer) {
            return false;
        }

        /** @var Product $subject */

        return $subject->getFarm() === $user->getFarm();
    }
}
