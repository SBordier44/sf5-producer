<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Doctrine\UuidGenerator;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Class Farm
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\FarmRepository")
 * @ORM\EntityListeners({"App\EntityListener\FarmListener"})
 */
class Farm
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="uuid")
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @Groups({"read"})
     */
    private ?UuidInterface $id = null;

    /**
     * @ORM\Column()
     * @Assert\NotBlank
     * @Groups({"read"})
     */
    private string $name = '';

    /**
     * @ORM\Column(nullable=true, type="text")
     * @Assert\NotBlank(groups={"edit"})
     */
    private ?string $description = null;

    /**
     * @ORM\Column(unique=true)
     * @Groups({"read"})
     */
    private string $slug;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Producer", mappedBy="farm")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private Producer $producer;

    /**
     * @ORM\Embedded(class="App\Entity\Address")
     * @Assert\Valid(groups={"edit"})
     * @Groups({"read"})
     */
    private ?Address $address = null;

    /**
     * @ORM\Embedded(class="Image")
     * @Assert\Valid(groups={"edit"})
     */
    private Image $image;

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
     * @return UuidInterface|null
     */
    public function getId(): ?UuidInterface
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return Farm
     */
    public function setName(string $name): Farm
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

    /**
     * @return Image
     */
    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @param Image $image
     * @return Farm
     */
    public function setImage(Image $image): Farm
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     * @return Farm
     */
    public function setSlug(string $slug): Farm
    {
        $this->slug = $slug;
        return $this;
    }
}
