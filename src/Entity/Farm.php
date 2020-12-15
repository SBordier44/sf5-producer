<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Class Farm
 * @package App\Entity
 * @ORM\Entity()
 */
class Farm
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     * @Groups({"read"})
     */
    private Uuid $id;

    /**
     * @ORM\Column(nullable=true)
     * @Assert\NotBlank
     * @Groups({"read"})
     */
    private ?string $name = null;

    /**
     * @ORM\Column(nullable=true, type="text")
     * @Assert\NotBlank
     */
    private ?string $description = null;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Producer", mappedBy="farm")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Producer $producer;

    /**
     * @ORM\Embedded(class="App\Entity\Address")
     * @Assert\Valid
     * @Groups({"read"})
     */
    private ?Address $address = null;

    /**
     * @return Address|null
     */
    public function getAddress(): ?Address
    {
        return $this->address;
    }

    /**
     * @param Address|null $address
     * @return Farm
     */
    public function setAddress(?Address $address): Farm
    {
        $this->address = $address;
        return $this;
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
     * @return Farm
     */
    public function setId(Uuid $id): Farm
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Farm
     */
    public function setName(?string $name): Farm
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return Producer
     */
    public function getProducer(): Producer
    {
        return $this->producer;
    }

    /**
     * @param Producer $producer
     * @return Farm
     */
    public function setProducer(Producer $producer): Farm
    {
        $this->producer = $producer;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     * @return Farm
     */
    public function setDescription(?string $description): Farm
    {
        $this->description = $description;
        return $this;
    }
}
