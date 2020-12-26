<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Farm;
use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class OrderRepository
 * @package App\Repository
 * @method findByOrder(Order $order): array<Order>
 * @method findByCustomer(Customer $customer): array<Order>
 * @method findByFarm(Farm $farm): array<Order>
 */
class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Order::class);
    }

    public function findByCustomerOrdered(Customer $customer)
    {
        return $this->createQueryBuilder('o')
            ->where("o.customer = '{$customer->getId()}'")
            ->orderBy('o.orderReference', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function getNextOrderReference(): ?string
    {
        return $this->createQueryBuilder('o')
            ->select('MAX(o.orderReference) as orderReference')
            ->getQuery()
            ->getResult()[0]['orderReference'];
    }
}
