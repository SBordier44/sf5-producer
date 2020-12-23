<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Product
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @ORM\EntityListeners({"App\EntityListener\ProductListener"})
 */
class Product
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     */
    private UuidInterface $id;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     */
    private string $name = '';

    /**
     * @ORM\Column
     * @Assert\NotBlank
     */
    private string $description = '';

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank
     * @Assert\GreaterThanOrEqual(0)
     */
    private int $quantity = 0;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Farm")
     * @ORM\JoinColumn(onDelete="CASCADE", nullable=false)
     */
    private ?Farm $farm = null;

    /**
     * @ORM\Embedded(class="Price")
     * @Assert\Valid
     */
    private Price $price;

    /**
     * @ORM\Embedded(class="Image")
     * @Assert\Valid
     */
    private ?Image $image = null;

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Product
     */
    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Product
     */
    public function setDescription(string $description): Product
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return Product
     */
    public function setQuantity(int $quantity): Product
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return Farm|null
     */
    public function getFarm(): ?Farm
    {
        return $this->farm;
    }

    /**
     * @param Farm|null $farm
     * @return Product
     */
    public function setFarm(?Farm $farm): Product
    {
        $this->farm = $farm;
        return $this;
    }

    /**
     * @return Price
     */
    public function getPrice(): Price
    {
        return $this->price;
    }

    /**
     * @param Price $price
     * @return Product
     */
    public function setPrice(Price $price): Product
    {
        $this->price = $price;
        return $this;
    }

    public function getPriceIncludingTaxes(): float
    {
        return ($this->price->getUnitPrice() * $this->price->getVat()) / 100;
    }

    /**
     * @return Image|null
     */
    public function getImage(): ?Image
    {
        return $this->image;
    }

    /**
     * @param Image|null $image
     * @return Product
     */
    public function setImage(?Image $image): Product
    {
        $this->image = $image;
        return $this;
    }
}
