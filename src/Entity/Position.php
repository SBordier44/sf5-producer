<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Position
 * @package App\Entity
 * @ORM\Embeddable
 */
class Position
{
    /**
     * @ORM\Column(type="decimal", precision=16, scale=13, nullable=true)
     * @Assert\NotBlank(groups={"edit"})
     * @Groups({"read"})
     */
    private ?float $latitude = null;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=13, nullable=true)
     * @Assert\NotBlank(groups={"edit"})
     * @Groups({"read"})
     */
    private ?float $longitude = null;

    /**
     * @return float|null
     */
    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    /**
     * @param float|string|null $longitude
     * @return Position
     */
    public function setLongitude($longitude): Position
    {
        $this->longitude = (float)$longitude;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    /**
     * @param float|string|null $latitude
     * @return Position
     */
    public function setLatitude($latitude): Position
    {
        $this->latitude = (float)$latitude;
        return $this;
    }
}
