<?php

declare(strict_types=1);

namespace App\Workflow;

use App\Entity\Order;
use DateTimeImmutable;
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

    /** @codeCoverageIgnore  */
    public static function getSubscribedEvents(): array
    {
        return [
            'workflow.order.completed.cancel' => 'onCancel',
            'workflow.order.completed.refuse' => 'onRefuse',
            'workflow.order.completed.accept' => 'onAccept',
            'workflow.order.completed.process' => 'onProcess',
            'workflow.order.completed.ready' => 'onReady',
            'workflow.order.completed.deliver' => 'onDeliver',
        ];
    }

    public function onCancel(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();

        $order->setCanceledAt(new DateTimeImmutable());

        $this->entityManager->flush();
    }

    public function onRefuse(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();

        $order->setRefusedAt(new DateTimeImmutable());

        $this->entityManager->flush();
    }

    public function onAccept(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();

        $order->setAcceptedAt(new DateTimeImmutable());

        $this->entityManager->flush();
    }

    public function onProcess(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();

        $order->setProcessingStartedAt(new DateTimeImmutable());

        $this->entityManager->flush();
    }

    public function onReady(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();

        $order->setProcessingCompletedAt(new DateTimeImmutable());

        $this->entityManager->flush();
    }

    public function onDeliver(Event $event): void
    {
        /** @var Order $order */
        $order = $event->getSubject();

        $order->setIssuedAt(new DateTimeImmutable());

        $this->entityManager->flush();
    }
}
