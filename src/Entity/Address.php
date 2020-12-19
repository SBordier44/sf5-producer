<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Address
 * @package App\Entity
 * @ORM\Embeddable
 */
class Address
{
    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank(groups={"edit"})
     * @Groups({"read"})
     */
    private ?string $address = null;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"read"})
     */
    private ?string $addressExtra = null;

    /**
     * @ORM\Column(length=5, nullable=true)
     * @Assert\NotBlank(groups={"edit"})
     * @Assert\Regex(pattern="/^[A-Za-z0-9]{5}$/", message="Code postal invalide.")
     * @Groups({"read"})
     */
    private ?string $zipCode = null;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank(groups={"edit"})
     * @Groups({"read"})
     */
    private ?string $city = null;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank(groups={"edit"})
     * @Groups({"read"})
     */
    private ?string $region = null;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank(groups={"edit"})
     * @Groups({"read"})
     */
    private ?string $country = null;

    /**
     * @ORM\Embedded(class="Position")
     * @Assert\Valid(groups={"edit"})
     * @Groups({"read"})
     */
    private ?Position $position = null;

    /**
     * @ORM\Column(length=10, nullable=true)
     * @Assert\NotBlank(groups={"edit"})
     * @Assert\Regex(pattern="/^[0-9]{10}$/")
     * @Groups({"read"})
     */
    private ?string $phone = null;

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }

    /**
     * @param string|null $address
     * @return Address
     */
    public function setAddress(?string $address): Address
    {
        $this->address = $address;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddressExtra(): ?string
    {
        return $this->addressExtra;
    }

    /**
     * @param string|null $addressExtra
     * @return Address
     */
    public function setAddressExtra(?string $addressExtra): Address
    {
        $this->addressExtra = $addressExtra;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    /**
     * @param string|null $zipCode
     * @return Address
     */
    public function setZipCode(?string $zipCode): Address
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string|null $city
     * @return Address
     */
    public function setCity(?string $city): Address
    {
        $this->city = $city;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRegion(): ?string
    {
        return $this->region;
    }

    /**
     * @param string|null $region
     * @return Address
     */
    public function setRegion(?string $region): Address
    {
        $this->region = $region;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getCountry(): ?string
    {
        return $this->country;
    }

    /**
     * @param string|null $country
     * @return Address
     */
    public function setCountry(?string $country): Address
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return Position|null
     */
    public function getPosition(): ?Position
    {
        return $this->position;
    }

    /**
     * @param Position $position
     * @return Address
     */
    public function setPosition(Position $position): Address
    {
        $this->position = $position;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhone(): ?string
    {
        return $this->phone;
    }

    /**
     * @param string|null $phone
     * @return Address
     */
    public function setPhone(?string $phone): Address
    {
        $this->phone = $phone;
        return $this;
    }
}
