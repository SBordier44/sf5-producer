<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

/**
 * Class Order
 * @package App\Entity
 * @ORM\Entity
 * @ORM\Table(name="orders")
 */
class Order
{
    /**
     * @var Uuid
     * @ORM\Id
     * @ORM\Column(type="uuid")
     */
    private Uuid $id;
    /**
     * @var string
     * @ORM\Column
     */
    private string $state = 'created';

    /**
     * @var DateTimeImmutable
     * @ORM\Column(type="datetime_immutable")
     */
    private DateTimeImmutable $createdAt;

    /**
     * @var DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $canceledAt = null;

    /**
     * @var Customer
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Customer $customer;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\OrderLine", mappedBy="order", cascade={"persist"})
     */
    private Collection $lines;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->lines = new ArrayCollection();
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @param Uuid $id
     * @return Order
     */
    public function setId(Uuid $id): Order
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     * @return Order
     */
    public function setState(string $state): Order
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return DateTimeImmutable
     */
    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    /**
     * @param DateTimeImmutable $createdAt
     * @return Order
     */
    public function setCreatedAt(DateTimeImmutable $createdAt): Order
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Customer
     */
    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    /**
     * @param Customer $customer
     * @return Order
     */
    public function setCustomer(Customer $customer): Order
    {
        $this->customer = $customer;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getLines()
    {
        return $this->lines;
    }

    public function getNumberOfProducts(): int
    {
        return array_sum($this->lines->map(fn(OrderLine $orderLine) => $orderLine->getQuantity())->toArray());
    }

    public function getTotalIncludingTaxes(): float
    {
        return array_sum(
            $this->lines->map(fn(OrderLine $orderLine) => $orderLine->getTotalIncludingTaxes())->toArray()
        );
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getCanceledAt(): ?DateTimeImmutable
    {
        return $this->canceledAt;
    }

    /**
     * @param DateTimeImmutable $canceledAt
     * @return Order
     */
    public function setCanceledAt(DateTimeImmutable $canceledAt): Order
    {
        $this->canceledAt = $canceledAt;
        return $this;
    }
}
