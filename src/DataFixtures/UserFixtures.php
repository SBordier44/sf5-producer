<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use App\Entity\Producer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Uid\Uuid;

class UserFixtures extends Fixture
{
    private UserPasswordEncoderInterface $userPasswordEncoder;

    public function __construct(UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $producer = (new Producer())
            ->setId(Uuid::v4())
            ->setEmail('producer@email.com')
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setRegisteredAt(new \DateTimeImmutable());
        $producer->setPassword($this->userPasswordEncoder->encodePassword($producer, 'password'));
        $manager->persist($producer);

        $customer = (new Customer())
            ->setId(Uuid::v4())
            ->setEmail('customer@email.com')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setRegisteredAt(new \DateTimeImmutable());
        $customer->setPassword($this->userPasswordEncoder->encodePassword($customer, 'password'));
        $manager->persist($customer);

        $manager->flush();
    }
}
