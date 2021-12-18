<?php

declare(strict_types=1);

namespace App\Entity;

use App\EntityListener\ProductListener;
use App\Repository\ProductRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\EntityListeners([ProductListener::class])]
class Product
{
    #[ORM\Id]
    #[ORM\Column(type: 'uuid', unique: true)]
    private ?Uuid $id;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $name = null;

    #[ORM\Column(type: Types::STRING)]
    private ?string $description = null;

    #[ORM\Column(type: Types::INTEGER)]
    private int $quantity = 0;

    #[ORM\ManyToOne(targetEntity: Farm::class)]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Farm $farm = null;

    #[ORM\Embedded(class: Price::class)]
    private ?Price $price = null;

    #[ORM\Embedded(class: Image::class)]
    private ?Image $image = null;

    public function __construct()
    {
        $this->id = Uuid::v4();
    }

    public function getId(): ?Uuid
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): Product
    {
        $this->description = $description;
        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(?int $quantity): Product
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getFarm(): ?Farm
    {
        return $this->farm;
    }

    public function setFarm(?Farm $farm): Product
    {
        $this->farm = $farm;
        return $this;
    }

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): Product
    {
        $this->price = $price;
        return $this;
    }

    #[Pure]
    public function getPriceIncludingTaxes(): float
    {
        if ($this->price) {
            $unitPrice = $this->price->getUnitPrice() / 100;
            $vat = $unitPrice * $this->price->getVat() / 100;
            return $unitPrice + $vat;
        }
        return 0.00;
    }

    #[Pure]
    public function getTaxesAmount(): float
    {
        if ($this->price) {
            $unitPrice = $this->price->getUnitPrice() / 100;
            return $unitPrice * $this->price->getVat() / 100;
        }
        return 0.00;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): Product
    {
        $this->image = $image;
        return $this;
    }
}
