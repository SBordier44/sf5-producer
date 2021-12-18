<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Order;
use App\Repository\OrderRepository;

class OrderListener
{
    public function __construct(private OrderRepository $orderRepository)
    {
    }

    public function prePersist(Order $order): void
    {
        if ($this->orderRepository->getNextOrderReference() !== null) {
            $order->setOrderReference((int)$this->orderRepository->getNextOrderReference() + 1);
        } else {
            $order->setOrderReference(581457564);
        }
    }
}
