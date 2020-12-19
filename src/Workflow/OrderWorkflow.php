<?php

namespace App\Workflow;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Workflow\Event\Event;

class OrderWorkflow implements EventSubscriberInterface
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.order.completed.cancel' => 'onCancel'
        ];
    }

    public function onCancel(Event $event): void
    {
        /** @var Order $roder */
        $order = $event->getSubject();
        $order->setCanceledAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }
}
