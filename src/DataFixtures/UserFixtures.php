<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Customer;
use App\Entity\Position;
use App\Entity\Producer;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Yaml\Yaml;

class UserFixtures extends Fixture
{
    private Generator $faker;
    private array $producers;

    public function __construct(private UserPasswordHasherInterface $passwordHasher, KernelInterface $kernel)
    {
        $this->faker = Factory::create('fr_FR');
        $this->producers = Yaml::parseFile(
            $kernel->getProjectDir() . '/src/DataFixtures/datas/producers.yaml'
        )['producers'];
    }

    public function load(ObjectManager $manager): void
    {
        $producerAddress = (object)$this->producers[0];

        $producer = (new Producer())
            ->setEmail('producer@email.com')
            ->setFirstName($this->faker->firstName)
            ->setLastName($this->faker->lastName)
            ->setIsVerified(true)
            ->setRegisteredAt(new DateTimeImmutable());

        $producer->setPassword($this->passwordHasher->hashPassword($producer, 'password'));

        $producer->getFarm()
            ->setDescription($this->faker->sentence())
            ->setName($producerAddress->name)
            ->setSiret($producerAddress->siret);

        $position = (new Position())
            ->setLongitude($producerAddress->lng)
            ->setLatitude($producerAddress->lat);

        $address = (new Address())
            ->setAddress($producerAddress->address)
            ->setCity($producerAddress->city)
            ->setZipCode($producerAddress->postalCode)
            ->setCountry($producerAddress->country)
            ->setPhone($producerAddress->phone)
            ->setPosition($position);

        $producer->getFarm()->setAddress($address);

        $manager->persist($producer);

        $manager->flush();

        for ($i = 1; $i <= 6; $i++) {
            $producerAddress = (object)$this->producers[$i];

            $producer = (new Producer())
                ->setEmail('producer' . $i . '@email.com')
                ->setFirstName($this->faker->firstName)
                ->setLastName($this->faker->lastName)
                ->setIsVerified($this->faker->boolean())
                ->setRegisteredAt(new DateTimeImmutable());

            $producer->setPassword($this->passwordHasher->hashPassword($producer, 'password'));

            $producer->getFarm()
                ->setDescription($this->faker->sentence())
                ->setName($producerAddress->name)
                ->setSiret($producerAddress->siret);

            $position = (new Position())
                ->setLongitude($producerAddress->lng)
                ->setLatitude($producerAddress->lat);

            $address = (new Address())
                ->setAddress($producerAddress->address)
                ->setCity($producerAddress->city)
                ->setZipCode($producerAddress->postalCode)
                ->setCountry($producerAddress->country)
                ->setPhone($producerAddress->phone)
                ->setPosition($position);

            $producer->getFarm()->setAddress($address);

            $manager->persist($producer);

            $manager->flush();
        }

        $customer = (new Customer())
            ->setEmail('customer@email.com')
            ->setFirstName($this->faker->firstName)
            ->setLastName($this->faker->lastName)
            ->setIsVerified(true)
            ->setRegisteredAt(new DateTimeImmutable());

        $customer->setPassword($this->passwordHasher->hashPassword($customer, 'password'));

        $manager->persist($customer);

        $manager->flush();

        for ($i = 1; $i <= 3; $i++) {
            $customer = (new Customer())
                ->setEmail("customer$i@email.com")
                ->setFirstName($this->faker->firstName)
                ->setLastName($this->faker->lastName)
                ->setIsVerified($this->faker->boolean())
                ->setRegisteredAt(new DateTimeImmutable());

            $customer->setPassword($this->passwordHasher->hashPassword($customer, 'password'));

            $manager->persist($customer);

            $manager->flush();
        }
    }
}
