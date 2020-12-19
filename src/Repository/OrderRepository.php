<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Farm;
use App\Entity\Order;
use App\Entity\Producer;
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
}
