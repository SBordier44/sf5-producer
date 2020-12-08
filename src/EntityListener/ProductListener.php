<?php

namespace App\EntityListener;

use App\Entity\Product;
use Symfony\Component\Security\Core\Security;

class ProductListener
{

    /**
     * @var Security
     */
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function prePersist(Product $product)
    {
        if ($product->getFarm() !== null) {
            return;
        }
        if ($this->security->getUser()) {
            $product->setFarm($this->security->getUser()->getFarm());
        }
    }
}
