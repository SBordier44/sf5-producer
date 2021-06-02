<?php

declare(strict_types=1);

namespace App\Security\Voter;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Producer;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Workflow\WorkflowInterface;

class OrderVoter extends Voter
{
    public const CANCEL = 'cancel';
    public const REFUSE = 'refuse';
    public const ACCEPT = 'accept';
    public const PROCESS = 'process';
    public const READY = 'ready';
    public const DELIVER = 'deliver';
    public const PRODUCER_VIEW = 'producer_view';

    private WorkflowInterface $orderStateMachine;

    public function __construct(WorkflowInterface $orderStateMachine)
    {
        $this->orderStateMachine = $orderStateMachine;
    }

    protected function supports(string $attribute, $subject): bool
    {
        return $subject instanceof Order
            && in_array(
                $attribute,
                [
                    self::CANCEL,
                    self::REFUSE,
                    self::ACCEPT,
                    self::PROCESS,
                    self::READY,
                    self::DELIVER,
                    self::PRODUCER_VIEW
                ]
            );
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        /** @var Order $subject */
        switch ($attribute) {
            case self::CANCEL:
                return $user instanceof Customer
                    && $user === $subject->getCustomer()
                    && $this->orderStateMachine->can($subject, self::CANCEL);

            case self::REFUSE:
                return $user instanceof Producer
                    && $user->getFarm() === $subject->getFarm()
                    && $this->orderStateMachine->can($subject, self::REFUSE);

            case self::ACCEPT:
                return $user instanceof Producer
                    && $user->getFarm() === $subject->getFarm()
                    && $this->orderStateMachine->can($subject, self::ACCEPT);

            case self::PROCESS:
                return $user instanceof Producer
                    && $user->getFarm() === $subject->getFarm()
                    && $this->orderStateMachine->can($subject, self::PROCESS);

            case self::READY:
                return $user instanceof Producer
                    && $user->getFarm() === $subject->getFarm()
                    && $this->orderStateMachine->can($subject, self::READY);

            case self::DELIVER:
                return $user instanceof Producer
                    && $user->getFarm() === $subject->getFarm()
                    && $this->orderStateMachine->can($subject, self::DELIVER);

            case self::PRODUCER_VIEW:
                if ($user && $user instanceof Customer) {
                    return $user->getId() === $subject->getCustomer()->getId();
                }

                if ($user && $user instanceof Producer) {
                    return $user->getId() === $subject->getFarm()->getProducer()->getId();
                }

                return false;
        }

        /** @codeCoverageIgnore */
        throw new AccessDeniedException("Vous n'avez pas accès à cette ressource.");
    }
}
