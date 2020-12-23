<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Farm;
use App\Entity\Order;
use App\Entity\OrderLine;
use App\Entity\Producer;
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
        $producer = $manager->getRepository(Producer::class)->findOneByEmail('producer@email.com');
        $products = $manager->getRepository(Product::class)->findBy(['farm' => $producer->getFarm()], [], 0, 5);
        $customer = $manager->getRepository(Customer::class)->findOneByEmail('customer@email.com');

        $order = (new Order())
            ->setCustomer($customer)
            ->setFarm($producer->getFarm());
        foreach ($products as $product) {
            $line = (new OrderLine())
                ->setOrder($order)
                ->setQuantity(random_int(1, 5))
                ->setProduct($product)
                ->setPrice($product->getPrice());
            $order->getLines()->add($line);
        }
        $order->setState('accepted');
        $manager->persist($order);
        $manager->flush();


        $customers = $manager->getRepository(Customer::class)->findAll();
        $farms = $manager->getRepository(Farm::class)->findAll();

        /** @var Customer $customer */
        foreach ($customers as $k => $customer) {
            foreach ($farms as $farm) {
                $products = $manager->getRepository(Product::class)->findBy(["farm" => $farm], [], 0, 5);

                $order = (new Order())
                    ->setCustomer($customer)
                    ->setFarm($farm);
                $manager->persist($order);

                foreach ($products as $product) {
                    $line = (new OrderLine())
                        ->setOrder($order)
                        ->setQuantity(random_int(1, 5))
                        ->setProduct($product)
                        ->setPrice($product->getPrice());
                    $order->getLines()->add($line);
                }
            }
        }
        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [UserFixtures::class, ProductFixtures::class];
    }
}
