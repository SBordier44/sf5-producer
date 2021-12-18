<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Farm;
use App\Entity\Producer;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Farm|null find($id, $lockMode = null, $lockVersion = null)
 * @method Farm|null findOneBy(array $criteria, array $orderBy = null)
 * @method Farm|null findOneByProducer(Producer $producer)
 * @method Farm[]    findAll()
 * @method Farm[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FarmRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Farm::class);
    }

    public function getNextSlug(string $slug): string
    {
        $findSlugs = $this->createQueryBuilder('f')
            ->select('f.slug')
            ->where('lower(f.slug) like lower(:pattern)')
            ->setParameter('pattern', $slug . '%')
            ->getQuery()
            ->getScalarResult();

        if (!$findSlugs) {
            return $slug;
        }

        $findSlugs = array_map(
            function (string $findSlug) use ($slug) {
                preg_match("/^" . $slug . "-([0-9]*)$/", $findSlug, $matches);
                return !isset($matches[1]) ? 0 : (int)$matches[1];
            },
            array_column($findSlugs, 'slug')
        );

        rsort($findSlugs);

        return sprintf('%s-%d', $slug, $findSlugs[0] + 1);
    }
}
