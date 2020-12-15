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
     * @Assert\NotBlank
     * @Groups({"read"})
     */
    private ?float $latitude = null;

    /**
     * @ORM\Column(type="decimal", precision=16, scale=13, nullable=true)
     * @Assert\NotBlank
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
     * @param float|null $longitude
     * @return Position
     */
    public function setLongitude(?float $longitude): Position
    {
        $this->longitude = $longitude;
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
     * @param float|null $latitude
     * @return Position
     */
    public function setLatitude(?float $latitude): Position
    {
        $this->latitude = $latitude;
        return $this;
    }
}
