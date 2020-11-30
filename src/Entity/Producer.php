<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Class Producer
 * @package App\Entity
 * @ORM\Entity
 * @ORM\EntityListeners({"App\EntityListener\ProducerListener"})
 */
class Producer extends User
{
    public const ROLE = 'producer';

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Farm", cascade={"persist"}, inversedBy="producer")
     */
    private Farm $farm;

    public function getRoles(): array
    {
        return ['ROLE_PRODUCER'];
    }

    /**
     * @return Farm
     */
    public function getFarm(): Farm
    {
        return $this->farm;
    }

    /**
     * @param Farm $farm
     * @return Producer
     */
    public function setFarm(Farm $farm): Producer
    {
        $this->farm = $farm;
        return $this;
    }
}
