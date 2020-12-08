<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Price
 * @package App\Entity
 * @ORM\Embeddable
 */
class Price
{
    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(0)
     */
    private int $unitPrice = 0;

    /**
     * @ORM\Column(type="decimal", precision=5, scale=2)
     * @Assert\NotBlank
     */
    private float $vat = 0.0;

    /**
     * @return int
     */
    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    /**
     * @param int $unitPrice
     * @return Price
     */
    public function setUnitPrice(int $unitPrice): Price
    {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * @return float
     */
    public function getVat(): float
    {
        return $this->vat;
    }

    /**
     * @param float $vat
     * @return Price
     */
    public function setVat(float $vat): self
    {
        $this->vat = $vat;
        return $this;
    }
}
