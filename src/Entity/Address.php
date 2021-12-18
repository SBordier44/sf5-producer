<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Embeddable]
class Address
{
    #[ORM\Column]
    #[Groups(['json_read'])]
    private ?string $address = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['json_read'])]
    private ?string $addressExtra = null;

    #[ORM\Column(length: 5)]
    #[Groups(['json_read'])]
    private ?string $zipCode = null;

    #[ORM\Column]
    #[Groups(['json_read'])]
    private ?string $city = null;

    #[ORM\Column]
    #[Groups(['json_read'])]
    private string $country = 'France';

    #[ORM\Embedded(class: Position::class)]
    #[Groups(['json_read'])]
    private ?Position $position = null;

    #[ORM\Column(length: 30)]
    private ?string $phone = null;

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): Address
    {
        $this->address = $address;
        return $this;
    }

    public function getAddressExtra(): ?string
    {
        return $this->addressExtra;
    }

    public function setAddressExtra(?string $addressExtra): Address
    {
        $this->addressExtra = $addressExtra;
        return $this;
    }

    public function getZipCode(): ?string
    {
        return $this->zipCode;
    }

    public function setZipCode(?string $zipCode): Address
    {
        $this->zipCode = $zipCode;
        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): Address
    {
        $this->city = $city;
        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(?string $country): Address
    {
        $this->country = $country;
        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): Address
    {
        $this->position = $position;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): Address
    {
        $this->phone = $phone;
        return $this;
    }

    public static function buildReadableSireneAddressStreet(array $data): string
    {
        $address = '';

        foreach ($data as $addressLine) {
            if ($addressLine) {
                $address .= $addressLine . ' ';
            }
        }

        return $address;
    }
}
