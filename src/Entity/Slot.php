<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Slot
 * @package App\Entity
 * @ORM\Entity
 */
class Slot
{
    /**
     * @var UuidInterface
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @ORM\Column(type="uuid")
     */
    private UuidInterface $id;

    /**
     * @var \DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private \DateTimeImmutable $startedAt;

    /**
     * @var Order
     * @ORM\ManyToOne(targetEntity="App\Entity\Order", inversedBy="slots")
     */
    private Order $order;

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return \DateTimeImmutable
     */
    public function getStartedAt(): \DateTimeImmutable
    {
        return $this->startedAt;
    }

    /**
     * @param \DateTimeImmutable $startedAt
     * @return Slot
     */
    public function setStartedAt(\DateTimeImmutable $startedAt): Slot
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @param Order $order
     * @return Slot
     */
    public function setOrder(Order $order): Slot
    {
        $this->order = $order;
        return $this;
    }
}
