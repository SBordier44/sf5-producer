<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Customer;
use App\Entity\Position;
use App\Entity\Producer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

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
            ->setEmail('producer@email.com')
            ->setFirstName('Jane')
            ->setLastName('Doe')
            ->setRegisteredAt(new \DateTimeImmutable());
        $producer->setPassword($this->userPasswordEncoder->encodePassword($producer, 'password'));
        $producer->getFarm()->setName('Ferme');
        $position = (new Position())
            ->setLongitude(-1.44995)
            ->setLatitude(47.16075);
        $address = (new Address())
            ->setAddress('28 Route de Saint-Fiacre')
            ->setCity('Vertou')
            ->setZipCode('44120')
            ->setCountry('France')
            ->setPhone('0613937893')
            ->setRegion('Loire-Atlantique, Pays de la Loire')
            ->setPosition($position);
        $producer->getFarm()->setAddress($address);
        $manager->persist($producer);
        $manager->flush();

        for ($i = 1; $i <= 19; $i++) {
            $producer = (new Producer())
                ->setEmail('producer' . $i . '@email.com')
                ->setFirstName('Jane')
                ->setLastName('Doe')
                ->setRegisteredAt(new \DateTimeImmutable());
            $producer->setPassword($this->userPasswordEncoder->encodePassword($producer, 'password'));
            $producer->getFarm()->setName('Ferme');
            $position = (new Position())
                ->setLongitude(-1.44995)
                ->setLatitude(47.16075);
            $address = (new Address())
                ->setAddress('28 Route de Saint-Fiacre')
                ->setCity('Vertou')
                ->setZipCode('44120')
                ->setCountry('France')
                ->setPhone('0613937893')
                ->setRegion('Loire-Atlantique, Pays de la Loire')
                ->setPosition($position);
            $producer->getFarm()->setAddress($address);
            $manager->persist($producer);
            $manager->flush();
        }

        $customer = (new Customer())
            ->setEmail('customer@email.com')
            ->setFirstName('John')
            ->setLastName('Doe')
            ->setRegisteredAt(new \DateTimeImmutable());
        $customer->setPassword($this->userPasswordEncoder->encodePassword($customer, 'password'));
        $manager->persist($customer);
        $manager->flush();
    }
}
