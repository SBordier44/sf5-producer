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
            'workflow.order.completed.cancel' => 'onCancel',
            'workflow.order.completed.refuse' => 'onRefuse',
            'workflow.order.completed.settle' => 'onSettle'
        ];
    }

    public function onCancel(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $order->setCanceledAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }

    public function onRefuse(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $order->setRefusedAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }

    public function onSettle(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();
        $order->setSettledAt(new \DateTimeImmutable());
        $this->entityManager->flush();
    }
}
