<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Farm;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

/**
 * Class OrderFixtures
 * @package App\DataFixtures
 */
class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $states = [
            'created',
            'accepted',
            'refused',
            'canceled',
            'settled',
            'processing',
            'ready',
            'issued'
        ];

        $customers = $manager->getRepository(Customer::class)->findAll();
        $farms = $manager->getRepository(Farm::class)->findAll();

        /** @var Customer $customer */
        foreach ($customers as $k => $customer) {
            foreach ($farms as $farm) {
                $products = $manager->getRepository(Product::class)->findBy(["farm" => $farm], [], 5, 0);

                foreach ($states as $state) {
                    $order = (new Order())
                        ->setCustomer($customer)
                        ->setFarm($farm);

                    foreach ($products as $product) {
                        $line = (new OrderLine())
                            ->setOrder($order)
                            ->setQuantity(random_int(1, 5))
                            ->setProduct($product)
                            ->setPrice($product->getPrice());
                        $order->getLines()->add($line);
                    }
                    $order->setState($state);
                    $manager->persist($order);
                    $manager->flush();
                }
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [UserFixtures::class, ProductFixtures::class];
    }
}
