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
     * @Assert\NotBlank
     * @Groups({"read"})
     */
    private ?string $address1 = null;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"read"})
     */
    private ?string $address2 = null;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"read"})
     */
    private ?string $address3 = null;

    /**
     * @ORM\Column(length=5, nullable=true)
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^[A-Za-z0-9]{5}$/")
     * @Groups({"read"})
     */
    private ?string $zipCode = null;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank
     * @Groups({"read"})
     */
    private ?string $city = null;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank
     * @Groups({"read"})
     */
    private ?string $region = null;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank
     * @Groups({"read"})
     */
    private ?string $country = null;

    /**
     * @ORM\Embedded(class="Position")
     * @Assert\Valid
     * @Groups({"read"})
     */
    private ?Position $position = null;

    /**
     * @ORM\Column(length=10, nullable=true)
     * @Assert\NotBlank
     * @Assert\Regex(pattern="/^[0-9]{10}$/")
     * @Groups({"read"})
     */
    private ?string $phone = null;

    /**
     * @ORM\Column(nullable=true)
     * @Groups({"read"})
     */
    private ?string $phone2 = null;

    /**
     * @return string|null
     */
    public function getAddress1(): ?string
    {
        return $this->address1;
    }

    /**
     * @param string|null $address1
     * @return Address
     */
    public function setAddress1(?string $address1): Address
    {
        $this->address1 = $address1;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress2(): ?string
    {
        return $this->address2;
    }

    /**
     * @param string|null $address2
     * @return Address
     */
    public function setAddress2(?string $address2): Address
    {
        $this->address2 = $address2;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getAddress3(): ?string
    {
        return $this->address3;
    }

    /**
     * @param string|null $address3
     * @return Address
     */
    public function setAddress3(?string $address3): Address
    {
        $this->address3 = $address3;
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

    /**
     * @return string|null
     */
    public function getPhone2(): ?string
    {
        return $this->phone2;
    }

    /**
     * @param string|null $phone2
     * @return Address
     */
    public function setPhone2(?string $phone2): Address
    {
        $this->phone2 = $phone2;
        return $this;
    }
}
