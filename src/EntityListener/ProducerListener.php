<?php

declare(strict_types=1);

namespace App\EntityListener;

use App\Entity\Farm;
use App\Entity\Producer;
use Symfony\Component\Uid\Uuid;

class ProducerListener
{
    public function prePersist(Producer $producer): void
    {
        $farm = (new Farm())
            ->setId(Uuid::v4())
            ->setProducer($producer);
        $producer->setFarm($farm);
    }
}
