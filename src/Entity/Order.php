<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;

/**
 * Class Order
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="orders")
 * @ORM\EntityListeners({"App\EntityListener\OrderListener"})
 */
class Order
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
     * @var int|null
     * @ORM\Column(type="bigint", nullable=false, unique=true)
     */
    private ?int $orderReference = null;

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
     * @var DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $refusedAt = null;

    /**
     * @var DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $settledAt = null;

    /**
     * @var DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $acceptedAt = null;

    /**
     * @var DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $processingStartedAt = null;

    /**
     * @var DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $processingCompletedAt = null;

    /**
     * @var DateTimeImmutable|null
     * @ORM\Column(type="datetime_immutable", nullable=true)
     */
    private ?DateTimeImmutable $issuedAt = null;

    /**
     * @var Customer
     * @ORM\ManyToOne(targetEntity="App\Entity\Customer")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Customer $customer;

    /**
     * @var Farm
     * @ORM\ManyToOne(targetEntity="App\Entity\Farm")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Farm $farm;

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
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @return int|null
     */
    public function getOrderReference(): ?int
    {
        return $this->orderReference;
    }

    /**
     * @param int|null $orderReference
     * @return Order
     */
    public function setOrderReference(?int $orderReference): Order
    {
        $this->orderReference = $orderReference;
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
     * @return DateTimeImmutable|null
     */
    public function getRefusedAt(): ?DateTimeImmutable
    {
        return $this->refusedAt;
    }

    /**
     * @param DateTimeImmutable|null $refusedAt
     * @return Order
     */
    public function setRefusedAt(?DateTimeImmutable $refusedAt): Order
    {
        $this->refusedAt = $refusedAt;
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

    public function getTotalWithoutTaxes(): float
    {
        return array_sum(
            $this->lines->map(fn(OrderLine $orderLine) => $orderLine->getTotal())->toArray()
        );
    }

    public function getTotalTaxes(): float
    {
        return array_sum(
            $this->lines->map(fn(OrderLine $orderLine) => $orderLine->getTaxesAmount())->toArray()
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

    /**
     * @return Farm
     */
    public function getFarm(): Farm
    {
        return $this->farm;
    }

    /**
     * @param Farm $farm
     * @return Order
     */
    public function setFarm(Farm $farm): Order
    {
        $this->farm = $farm;
        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getSettledAt(): ?DateTimeImmutable
    {
        return $this->settledAt;
    }

    /**
     * @param DateTimeImmutable|null $settledAt
     * @return Order
     */
    public function setSettledAt(?DateTimeImmutable $settledAt): Order
    {
        $this->settledAt = $settledAt;
        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getAcceptedAt(): ?DateTimeImmutable
    {
        return $this->acceptedAt;
    }

    /**
     * @param DateTimeImmutable|null $acceptedAt
     * @return Order
     */
    public function setAcceptedAt(?DateTimeImmutable $acceptedAt): Order
    {
        $this->acceptedAt = $acceptedAt;
        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getProcessingStartedAt(): ?DateTimeImmutable
    {
        return $this->processingStartedAt;
    }

    /**
     * @param DateTimeImmutable|null $processingStartedAt
     * @return Order
     */
    public function setProcessingStartedAt(?DateTimeImmutable $processingStartedAt): Order
    {
        $this->processingStartedAt = $processingStartedAt;
        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getProcessingCompletedAt(): ?DateTimeImmutable
    {
        return $this->processingCompletedAt;
    }

    /**
     * @param DateTimeImmutable|null $processingCompletedAt
     * @return Order
     */
    public function setProcessingCompletedAt(?DateTimeImmutable $processingCompletedAt): Order
    {
        $this->processingCompletedAt = $processingCompletedAt;
        return $this;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getIssuedAt(): ?DateTimeImmutable
    {
        return $this->issuedAt;
    }

    /**
     * @param DateTimeImmutable|null $issuedAt
     * @return Order
     */
    public function setIssuedAt(?DateTimeImmutable $issuedAt): Order
    {
        $this->issuedAt = $issuedAt;
        return $this;
    }
}
