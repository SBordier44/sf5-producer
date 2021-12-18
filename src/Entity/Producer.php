<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ProducerRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProducerRepository::class)]
class Producer extends User
{
    public const ROLE = 'producer';

    #[ORM\OneToOne(inversedBy: 'producer', targetEntity: Farm::class, cascade: ['persist'])]
    private Farm $farm;

    public function __construct()
    {
        parent::__construct();
        $this->farm = (new Farm())->setProducer($this);
    }

    public function getRoles(): array
    {
        return ['ROLE_PRODUCER', 'ROLE_USER'];
    }

    public function getFarm(): Farm
    {
        return $this->farm;
    }

    public function setFarm(Farm $farm): Producer
    {
        $this->farm = $farm;
        return $this;
    }
}
