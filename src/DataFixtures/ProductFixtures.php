<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Farm;
use App\Entity\Image;
use App\Entity\Price;
use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Uid\Uuid;

class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $farms = $manager->getRepository(Farm::class)->findAll();

        /** @var Farm $farm */
        foreach ($farms as $farm) {
            for ($i = 1; $i <= 10; $i++) {
                $product = new Product();
                $product->setFarm($farm);
                $product->setName("Product " . $i);
                $product->setDescription("Description");
                $price = new Price();
                $price->setUnitPrice(random_int(100, 150));
                $price->setVat(2.1);
                $product->setPrice($price);
                $product->setQuantity(random_int(0, 100));
                $image = new Image();
                $image->setFile($this->createImage());
                $product->setImage($image);
                $manager->persist($product);
            }
        }

        $manager->flush();
    }

    /**
     * @inheritDoc
     */
    public function getDependencies()
    {
        return [UserFixtures::class];
    }

    /**
     * @return UploadedFile
     */
    private function createImage(): UploadedFile
    {
        $filename = Uuid::v4() . '.png';
        copy(
            __DIR__ . '/../../public/uploads/TF300.png',
            __DIR__ . '/../../public/uploads/' . $filename
        );

        return new UploadedFile(
            __DIR__ . '/../../public/uploads/' . $filename,
            $filename,
            'image/png',
            null,
            true
        );
    }
}
