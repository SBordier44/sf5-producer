<?php

namespace App\DataFixtures;

use App\Entity\Farm;
use App\Entity\Price;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Uid\Uuid;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $farms = $manager->getRepository(Farm::class)->findAll();
        foreach ($farms as $farm) {
            for ($i = 1; $i <= 10; $i++) {
                $price = (new Price())
                    ->setUnitPrice(random_int(100, 1000))
                    ->setVat(2.1);
                $product = (new Product())
                    ->setId(Uuid::v4())
                    ->setFarm($farm)
                    ->setName("Produit $i")
                    ->setDescription("Description du produit $i")
                    ->setPrice($price)
                    ->setQuantity(random_int(5, 100));
                $manager->persist($product);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];
    }
}
