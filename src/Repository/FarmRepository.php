<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Farm;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Class FarmRepository
 * @package App\Repository
 * @method findByFarm(Farm $farm): array<Farm>
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
            ->where('REGEXP(f.slug, :pattern) > 0')
            ->setParameter('pattern', '^' . $slug)
            ->getQuery()
            ->getScalarResult();

        if (count($findSlugs) === 0) {
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
