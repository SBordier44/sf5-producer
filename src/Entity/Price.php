<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Price
{
    #[ORM\Column(type: Types::INTEGER)]
    private int $unitPrice = 0;

    #[ORM\Column(type: Types::DECIMAL, precision: 5, scale: 2)]
    private float $vat = 0.0;

    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): Price
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    public function getVat(): float
    {
        return $this->vat;
    }

    public function setVat(float $vat): Price
    {
        $this->vat = $vat;
        return $this;
    }
}
