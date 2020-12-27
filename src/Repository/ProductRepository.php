<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Farm;
use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ProductRepository
 * @package App\Repository
 * @method findByFarm(Farm $farm): array<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    private function findByStockIsOverZero(): QueryBuilder
    {
        return $this->createQueryBuilder('p')
            ->where('p.quantity > 0');
    }

    public function getOne(): ?Product
    {
        return $this->findByStockIsOverZero()
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getBy(array $wheres = []): array
    {
        $query = $this->findByStockIsOverZero();
        foreach ($wheres as $col => $val) {
            $query->where("p.{$col} = '{$val}'");
        }
        return $query->getQuery()->getResult();
    }

    public function getOneBy(array $wheres = []): ?Product
    {
        $query = $this->findByStockIsOverZero()
            ->setMaxResults(1);
        foreach ($wheres as $col => $val) {
            $query->where("p.{$col} = '{$val}'");
        }
        return $query->getQuery()->getOneOrNullResult();
    }
}
