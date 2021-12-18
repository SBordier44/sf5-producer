<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Customer;
use App\Entity\Order;
use App\Entity\Producer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Order|null find($id, $lockMode = null, $lockVersion = null)
 * @method Order|null findOneBy(array $criteria, array $orderBy = null)
 * @method Order[]    findAll()
 * @method Order[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
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

    public function getNextOrderReference(): ?int
    {
        return $this->createQueryBuilder('o')
            ->select('MAX(o.orderReference) as orderReference')
            ->getQuery()
            ->getResult()[0]['orderReference'];
    }

    public function getLastOrderForCustomer(Customer $customer): ?Order
    {
        return $this->createQueryBuilder('o')
            ->where("o.customer = '{$customer->getId()}'")
            ->orderBy('o.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getOrdersWaitValidationForProducer(Producer $producer): array
    {
        return $this->createQueryBuilder('o')
            ->where("o.farm = '{$producer->getFarm()->getId()}'")
            ->andWhere("o.state = 'created'")
            ->orderBy('o.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
