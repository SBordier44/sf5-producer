<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Producer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class ProducerRepository
 * @package App\Repository
 * @method findOneByEmail(string $email): ?Producer
 */
class ProducerRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producer::class);
    }
}
