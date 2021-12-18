<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Product;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Uid\Uuid;

class ProductListener
{
    public function __construct(
        private Security $security,
        private string $uploadWebDir,
        private string $uploadAbsoluteDir
    ) {
    }

    public function prePersist(Product $product): void
    {
        $this->upload($product);

        if ($product->getFarm() !== null) {
            return;
        }

        if ($this->security->getUser()) {
            $product->setFarm($this->security->getUser()->getFarm());
        }
    }

    public function preUpdate(Product $product): void
    {
        $this->upload($product);
    }

    private function upload(Product $product): void
    {
        if ($product->getImage() === null || $product->getImage()->getFile() === null) {
            return;
        }

        $filename = Uuid::v4() . '.' . $product->getImage()->getFile()->getClientOriginalExtension();

        $product->getImage()->getFile()->move($this->uploadAbsoluteDir, $filename);

        $product->getImage()->setPath($this->uploadWebDir . $filename);
    }
}
